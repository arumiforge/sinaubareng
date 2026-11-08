<?php

declare(strict_types=1);

namespace App\Curriculum\Import\Formats;

use App\Curriculum\Import\CurriculumFormat;
use App\Curriculum\Import\Data;
use App\Curriculum\Import\Grades;
use App\Curriculum\Import\ImportedFile;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\InvalidCurriculumFile;
use App\Curriculum\Import\SubjectLookup;
use App\Curriculum\Import\TopicInput;
use App\Curriculum\Import\TopicWriter;
use App\Curriculum\Import\VersionRegistry;
use App\Models\Subject;

/**
 * Topics in the data model's own shape (D-140): the TP/ATP drafts for the five SD
 * religions and the five MI subjects. `subjects[].allocations[]` are only used to check
 * the semester totals; allocations themselves come from the structure versions.
 */
final readonly class DataModelTopics implements CurriculumFormat
{
    public function __construct(private VersionRegistry $versions, private TopicWriter $topics, private SubjectLookup $subjects) {}

    public function supports(array $json): bool
    {
        return isset($json['version_code'], $json['subjects'][0]['topics']);
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $data = $file->data();
        $code = $data->string('version_code');
        $version = $this->versions->upsert($code, $data->nullableString('scope') ?? $code, $file, array_filter([
            'dataset' => $data->nullableString('dataset'),
            'source' => $data->raw('source'),
            'authoring' => $data->raw('authoring'),
        ], fn (mixed $value): bool => $value !== null), $report, $data->nullableString('status'));

        $topics = [];
        $targets = [];

        foreach ($data->objects('subjects') as $row) {
            $subject = $this->subject($row, $report);

            foreach ($row->has('allocations') ? $row->objects('allocations') : [] as $allocation) {
                $grade = Grades::grade($allocation->raw('grade'), $allocation->path);
                $targets["{$subject->code}|{$grade}|1"] = $allocation->int('s1_jp');
                $targets["{$subject->code}|{$grade}|2"] = $allocation->int('s2_jp');
            }

            foreach ($row->objects('topics') as $topic) {
                $topics[] = $this->topic($topic, $subject);
            }
        }

        $this->topics->write($version, $topics, $targets, $report);
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

    private function topic(Data $row, Subject $subject): TopicInput
    {
        $semester = $row->int('semester');

        if (! in_array($semester, [1, 2], true)) {
            throw new InvalidCurriculumFile("{$row->pathTo('semester')} must be 1 or 2.");
        }

        $element = $row->string('element');
        $subElement = $row->nullableString('sub_element');

        return new TopicInput(
            code: $row->string('code'),
            legacyCodes: $row->has('legacy_codes') ? $row->strings('legacy_codes') : [],
            subject: $subject,
            grade: Grades::grade($row->raw('grade'), $row->path),
            phase: Grades::phase($row->string('phase'), $row->path),
            semester: $semester,
            elements: $element,
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
            audit: $row->nullableObject('audit')?->toArray(),
            extras: $subElement === null ? null : ['sub_element' => $subElement],
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
}
