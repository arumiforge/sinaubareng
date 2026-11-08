<?php

declare(strict_types=1);

namespace App\Curriculum\Import\Formats;

use App\Curriculum\Import\CurriculumFormat;
use App\Curriculum\Import\Data;
use App\Curriculum\Import\Grades;
use App\Curriculum\Import\ImportedFile;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\InvalidCurriculumFile;
use App\Curriculum\Import\RowWriter;
use App\Curriculum\Import\SubjectLookup;
use App\Curriculum\Import\VersionRegistry;
use App\Models\CurriculumVersion;
use App\Models\LearningOutcome;
use App\Models\Subject;

/**
 * CP-only versions (D-226): the SD religion CP (BKPDM 020/2026, `religions[]`) and the
 * MI Kemenag CP (SK Dirjen 9941/2025, `subjects[]`, D-039). Each phase becomes an
 * optional summary row (element NULL) and one row per element or sub-element.
 */
final readonly class LearningOutcomes implements CurriculumFormat
{
    public function __construct(private VersionRegistry $versions, private RowWriter $rows, private SubjectLookup $subjects) {}

    public function supports(array $json): bool
    {
        return in_array($json['dataset'] ?? null, ['cp_pendidikan_agama_sd', 'cp_pai_bahasa_arab_mi'], true);
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $data = $file->data();
        $source = $data->object('source');
        $version = $this->versions->upsert(
            $data->string('version_code'),
            $source->string('title'),
            $file,
            ['dataset' => $data->string('dataset'), 'source' => $source->toArray()],
            $report,
            $data->nullableString('status'),
        );

        $key = $data->has('religions') ? 'religions' : 'subjects';
        $codes = [];

        foreach ($data->objects($key) as $subjectRow) {
            $name = $subjectRow->has('subject_name') ? $subjectRow->string('subject_name') : $subjectRow->string('name');
            $subject = $this->subjects->get($name, $subjectRow->path);
            $elements = array_map(fn (Data $element): string => $element->string('name'), $subjectRow->objects('elements'));

            foreach ($subjectRow->objects('phases') as $phaseRow) {
                $codes = [...$codes, ...$this->importPhase($version, $subject, $elements, $phaseRow, $report)];
            }
        }

        $stale = LearningOutcome::query()->whereBelongsTo($version)->whereNotIn('code', $codes)->pluck('code');

        foreach ($stale as $code) {
            $report->mismatch("CP row {$code} is in the database but not in the file; it is kept.");
        }
    }

    /**
     * @param  list<string>  $elements
     * @return list<string> the codes written
     */
    private function importPhase(CurriculumVersion $version, Subject $subject, array $elements, Data $phaseRow, ImportReport $report): array
    {
        $phase = Grades::phase($phaseRow->string('phase'), $phaseRow->path);

        foreach ($phaseRow->ints('grades') as $grade) {
            Grades::checkPhase($grade, $phase, "{$subject->code} CP {$phaseRow->path}", $report);
        }

        $order = 0;
        $codes = [];
        $prefix = "{$version->code}-{$subject->code}-{$phase->value}";
        $leadIn = $phaseRow->nullableString('lead_in');
        $intros = [];

        foreach ($phaseRow->has('element_intros') ? $phaseRow->objects('element_intros') : [] as $intro) {
            $intros[$intro->string('element')] = $intro->string('text');
        }

        if ($phaseRow->nullableString('summary') !== null) {
            $codes[] = $code = sprintf('%s-%02d', $prefix, $order);
            $this->rows->upsert(LearningOutcome::class, ['code' => $code], [
                'curriculum_version_id' => $version->id, 'subject_id' => $subject->id, 'phase' => $phase->value,
                'order' => $order, 'element' => null, 'sub_element' => null,
                'text' => $phaseRow->string('summary'), 'extras' => null,
            ], $report);
        }

        $skipped = 0;
        $reviewNotes = 0;

        foreach ($phaseRow->objects('outcomes') as $outcome) {
            if ($outcome->nullableString('text') === null) {
                if ($outcome->raw('no_cp_in_phase') !== true) {
                    throw new InvalidCurriculumFile("{$outcome->pathTo('text')} is empty without no_cp_in_phase.");
                }

                $skipped++;

                continue;
            }

            $element = $outcome->string('element');

            if (! in_array($element, $elements, true)) {
                $report->mismatch("{$subject->code} Fase {$phase->value}: element '{$element}' is not in the subject's element list.");
            }

            $extras = array_filter([
                'lead_in' => $leadIn,
                'element_intro' => $intros[$element] ?? null,
                'arabic_passages' => $outcome->has('arabic_passages') ? $outcome->list('arabic_passages') : null,
                'review_notes' => $outcome->has('review_notes') ? $outcome->strings('review_notes') : null,
            ], fn (mixed $value): bool => $value !== null && $value !== []);

            unset($intros[$element]); // printed once, before the element's first row

            $reviewNotes += isset($extras['review_notes']) ? 1 : 0;
            $codes[] = $code = sprintf('%s-%02d', $prefix, ++$order);

            $this->rows->upsert(LearningOutcome::class, ['code' => $code], [
                'curriculum_version_id' => $version->id, 'subject_id' => $subject->id, 'phase' => $phase->value,
                'order' => $order, 'element' => $element, 'sub_element' => $outcome->nullableString('sub_element'),
                'text' => $outcome->string('text'), 'extras' => $extras === [] ? null : $extras,
            ], $report);
        }

        if ($skipped > 0) {
            $report->note("{$subject->code} Fase {$phase->value}: {$skipped} sub-element(s) have no CP in this phase (no_cp_in_phase).");
        }

        if ($reviewNotes > 0) {
            $report->note("{$subject->code} Fase {$phase->value}: {$reviewNotes} CP row(s) carry review notes for Tim Validasi SIGRAK.");
        }

        return $codes;
    }
}
