<?php

declare(strict_types=1);

namespace App\Curriculum\Import\Formats;

use App\Curriculum\Import\CurriculumFormat;
use App\Curriculum\Import\Data;
use App\Curriculum\Import\Grades;
use App\Curriculum\Import\ImportedFile;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\InvalidCurriculumFile;
use App\Curriculum\Import\SourceOverrides;
use App\Curriculum\Import\SubjectLookup;
use App\Curriculum\Import\TopicInput;
use App\Curriculum\Import\TopicWriter;
use App\Curriculum\Import\VersionRegistry;
use App\Enums\SubjectKind;
use App\Models\Subject;

/**
 * SIGRAK curriculum files (D-363), in the data model's own shape (D-140): topics with
 * their TP, ATP, and fokus, one version per level and year (SIGRAK-SD-2026-R1,
 * SIGRAK-MI-2026-R1). Several files can share a version. `subjects[].allocations[]` are
 * the semester JP targets the topics are checked against (D-366); the allocations a
 * class uses come from the structure versions. Each subject's `curation` is kept in
 * the file's meta (D-364).
 */
final readonly class DataModelTopics implements CurriculumFormat
{
    public const DATASET = 'sigrak_curriculum_topics';

    private const CURATION_STATUSES = ['converted', 'in_review', 'curated'];

    public function __construct(private VersionRegistry $versions, private TopicWriter $topics, private SubjectLookup $subjects, private SourceOverrides $overrides) {}

    public function supports(array $json): bool
    {
        return isset($json['version_code'], $json['subjects'][0]['topics']);
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $data = $file->data();
        $dataset = $data->nullableString('dataset');

        if ($dataset !== self::DATASET) {
            throw new InvalidCurriculumFile("{$file->relativePath}: dataset '{$dataset}' is not a SIGRAK curriculum file; topics are imported only from dataset '".self::DATASET."' (D-363).");
        }

        $code = $data->string('version_code');
        $rows = $data->objects('subjects');
        $curation = [];

        foreach ($rows as $row) {
            $curation[$row->string('code')] = $this->curation($row);
        }

        $version = $this->versions->upsert($code, $data->nullableString('scope') ?? $code, $file, array_filter([
            'dataset' => $dataset,
            'part' => $data->nullableString('part'),
            'source' => $data->raw('source'),
            'authoring' => $data->raw('authoring'),
            'curation' => $curation,
        ], fn (mixed $value): bool => $value !== null), $report, $data->nullableString('status'));

        $topics = [];
        $targets = [];

        foreach ($rows as $row) {
            $subject = $this->subject($row, $report);

            foreach ($row->has('allocations') ? $row->objects('allocations') : [] as $allocation) {
                $grade = Grades::grade($allocation->raw('grade'), $allocation->path);
                $targets["{$subject->code}|{$grade}|1"] = $allocation->int('s1_jp');
                $targets["{$subject->code}|{$grade}|2"] = $allocation->int('s2_jp');
            }

            $flagged = [];

            foreach ($row->objects('topics') as $topic) {
                $input = $this->overrides->apply($this->topic($topic, $subject, $code, $report), $report);
                $topics[] = $input;
                $this->flag($input, $flagged, $report);
            }
        }

        $this->topics->write($version, $topics, $targets, $report);
    }

    /**
     * @return array{status: string, since: string|null, reviewed_by: string|null, reviewed_on: string|null, note: string|null}
     */
    private function curation(Data $row): array
    {
        if (! $row->has('curation')) {
            throw new InvalidCurriculumFile("{$row->pathTo('curation')} is missing; every subject has a curation status (D-364).");
        }

        $curation = $row->object('curation');
        $status = $curation->string('status');

        if (! in_array($status, self::CURATION_STATUSES, true)) {
            throw new InvalidCurriculumFile("{$curation->pathTo('status')} '{$status}' must be one of ".implode(', ', self::CURATION_STATUSES).'.');
        }

        return [
            'status' => $status,
            'since' => $curation->nullableString('since'),
            'reviewed_by' => $curation->nullableString('reviewed_by'),
            'reviewed_on' => $curation->nullableString('reviewed_on'),
            'note' => $curation->nullableString('note'),
        ];
    }

    private function subject(Data $row, ImportReport $report): Subject
    {
        $subject = $this->subjects->get($row->string('code'), $row->path);

        if (SubjectLookup::normalize($row->string('name')) !== SubjectLookup::normalize($subject->name)) {
            $report->mismatch("{$row->path}: subject {$subject->code} is named '{$row->string('name')}', the catalogue says '{$subject->name}'.");
        }

        if ($row->has('religion') && $row->string('religion') !== $subject->religion?->value) {
            $report->mismatch("{$row->path}: subject {$subject->code} has religion '{$row->string('religion')}', the catalogue says '".($subject->religion->value ?? 'none')."'.");
        }

        return $subject;
    }

    private function topic(Data $row, Subject $subject, string $version, ImportReport $report): TopicInput
    {
        $semester = $row->int('semester');

        if (! in_array($semester, [1, 2], true)) {
            throw new InvalidCurriculumFile("{$row->pathTo('semester')} must be 1 or 2.");
        }

        $code = $row->string('code');
        $this->checkSubjectCode($code, $version, $subject, $report);
        $subElement = $row->nullableString('sub_element');
        $extras = array_filter([
            ...($row->nullableObject('extras')?->toArray() ?? []),
            'sub_element' => $subElement,
        ], fn (mixed $value): bool => $value !== null);
        $notes = $row->nullableObject('curation_notes')?->toArray();

        return new TopicInput(
            code: $code,
            subject: $subject,
            grade: Grades::grade($row->raw('grade'), $row->path),
            phase: Grades::phase($row->string('phase'), $row->path),
            semester: $semester,
            elements: $row->string('element'),
            title: $row->string('title'),
            cpText: $row->string('cp_text'),
            atpCode: $row->string('atp_code'),
            orderInSemester: $row->int('order_in_semester'),
            jpTotal: $row->int('jp_total'),
            jpPerWeek: $row->int('jp_per_week'),
            weekStart: $row->int('week_start'),
            weekEnd: $row->int('week_end'),
            weeklyDistribution: $row->ints('weekly_distribution'),
            references: $row->object('references')->toArray(),
            curationNotes: $notes === [] ? null : $notes,
            extras: $extras === [] ? null : $extras,
            objectives: array_map(fn (Data $objective): array => [
                'code' => $objective->string('code'),
                'order' => $objective->int('order'),
                'text' => $objective->string('text'),
            ], $row->objects('learning_objectives')),
            focusItems: array_map(fn (Data $focus): array => [
                'code' => $focus->string('code'),
                'order' => $focus->int('order'),
                'title' => $focus->string('title'),
                'estimated_jp' => $focus->int('estimated_jp'),
                'allocation_status' => $focus->string('allocation_status'),
                'objective_codes' => $focus->has('objective_codes') ? $focus->strings('objective_codes') : null,
            ], $row->objects('focus_items')),
        );
    }

    /**
     * Topic codes look like <version>-<subject>-K<grade>-<nn>. The subject part must be
     * the catalogue code or an alias of the same subject (Seni dan Budaya uses its branch
     * codes), so a new code is reported for the catalogue to be corrected.
     */
    private function checkSubjectCode(string $code, string $version, Subject $subject, ImportReport $report): void
    {
        if (preg_match('/^'.preg_quote($version, '/').'-(.+)-K\d+-\d+$/', $code, $match) === 1 && $this->subjects->find($match[1])?->is($subject) !== true) {
            $report->mismatch("Topic {$code} uses subject code {$match[1]}; the catalogue code for {$subject->name} is {$subject->code}.");
        }
    }

    /**
     * Notes once per subject and grade where the topics are not plain intrakurikuler.
     *
     * @param  array<int, true>  $flagged
     */
    private function flag(TopicInput $topic, array &$flagged, ImportReport $report): void
    {
        if (isset($flagged[$topic->grade])) {
            return;
        }

        $flagged[$topic->grade] = true;
        $learningType = $topic->extras['learning_type'] ?? null;

        if ($topic->subject->kind === SubjectKind::Cocurricular) {
            $report->note("{$topic->subject->name} grade {$topic->grade}: topics are kept as kokurikuler content (D-208).");
        } elseif ($topic->grade <= 2 && in_array($topic->subject->code, ['BIG', 'IPAS'], true)) {
            $report->note("{$topic->subject->name} grade {$topic->grade}: optional content, off until a school switches it on (D-204).");
        } elseif (is_string($learningType) && mb_strtolower($learningType) === 'kokurikuler') {
            $report->note("{$topic->subject->name} grade {$topic->grade}: the file marks these topics as kokurikuler.");
        }
    }
}
