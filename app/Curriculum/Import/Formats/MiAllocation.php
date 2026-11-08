<?php

declare(strict_types=1);

namespace App\Curriculum\Import\Formats;

use App\Curriculum\Import\AllocationWriter;
use App\Curriculum\Import\CurriculumFormat;
use App\Curriculum\Import\Data;
use App\Curriculum\Import\Grades;
use App\Curriculum\Import\ImportedFile;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\SubjectLookup;
use App\Curriculum\Import\VersionRegistry;
use App\Enums\LearningType;
use App\Enums\SubjectKind;
use App\Models\CurriculumVersion;

/**
 * docs/data/curriculum/mi-allocation-kma-1503-2025.json (ALOKASI-MI-KMA-1503-2025):
 * MI intrakurikuler and kokurikuler JP per subject and grade. The muatan lokal range
 * goes to the version's meta. Koding dan KA runs through kokurikuler (D-208).
 */
final readonly class MiAllocation implements CurriculumFormat
{
    public function __construct(
        private VersionRegistry $versions,
        private AllocationWriter $allocations,
        private SubjectLookup $subjects,
    ) {}

    public function supports(array $json): bool
    {
        return ($json['dataset'] ?? null) === 'mi_allocation';
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $data = $file->data();
        $groups = $data->objects('grades');
        $localContent = [];

        foreach ($groups as $group) {
            $range = $group->object('muatan_lokal');

            foreach ($group->list('grades') as $grade) {
                $localContent[(string) Grades::grade($grade, $group->path)] = [
                    'min' => $range->int('intrakurikuler_min'),
                    'max' => $range->int('intrakurikuler_max'),
                ];
            }
        }

        $source = $data->object('source');
        $version = $this->versions->upsert($data->string('version_code'), $source->string('title'), $file, [
            'dataset' => $data->string('dataset'),
            'source' => $source->only(['title', 'issued_on', 'file', 'section']),
            'units' => $data->raw('units'),
            'local_content_jp_per_year' => $localContent,
        ], $report, $data->nullableString('status'));

        $written = [];

        foreach ($groups as $group) {
            $written = [...$written, ...$this->importGroup($version, $group, $report)];
        }

        $this->allocations->reportStale($version, $written, $report);
    }

    /**
     * @return list<int>
     */
    private function importGroup(CurriculumVersion $version, Data $group, ImportReport $report): array
    {
        $phase = Grades::phase($group->string('phase'), $group->path);
        $weeks = $group->int('weeks_per_year');
        $where = 'Kelas '.$group->string('grade');
        $required = ['intrakurikuler' => 0, 'kokurikuler' => 0];
        $written = [];

        foreach ($group->objects('subjects') as $row) {
            $subject = $this->subjects->get($row->string('key'), "{$where} ({$row->path})");
            $intra = $row->int('intrakurikuler');
            $co = $row->int('kokurikuler');

            if ($row->has('total') && $row->int('total') !== $intra + $co) {
                $report->mismatch("{$where} {$subject->code}: total {$row->int('total')} is not intrakurikuler {$intra} + kokurikuler {$co}.");
            }

            if ($row->has('intrakurikuler_per_week') && $row->number('intrakurikuler_per_week') * $weeks !== (float) $intra) {
                $report->mismatch("{$where} {$subject->code}: {$row->number('intrakurikuler_per_week')} JP per week does not give {$intra} JP over {$weeks} weeks.");
            }

            if ($row->string('group') === 'required') {
                $required['intrakurikuler'] += $intra;
                $required['kokurikuler'] += $co;
            }

            if ($subject->kind === SubjectKind::Cocurricular) {
                $report->note("{$where}: {$subject->name} ({$intra} JP per year, elective) is not imported as an intrakurikuler allocation; it runs through kokurikuler (D-208).");

                continue;
            }

            foreach ($group->list('grades') as $gradeValue) {
                $grade = Grades::grade($gradeValue, $where);
                Grades::checkPhase($grade, $phase, $where, $report);

                foreach ([LearningType::Intracurricular->value => $intra, LearningType::Cocurricular->value => $co] as $type => $jp) {
                    $allocation = $jp > 0 ? $this->allocations->writeYearly($version, $subject, $grade, $phase, LearningType::from($type), $jp, $weeks, $report) : null;

                    if ($allocation !== null) {
                        $written[] = $allocation->id;
                    }
                }
            }
        }

        $printed = $group->object('totals')->object('required');

        foreach (['intrakurikuler', 'kokurikuler'] as $column) {
            if ($printed->int($column) !== $required[$column]) {
                $report->mismatch("{$where}: printed {$column} total for the required subjects is {$printed->int($column)}, the rows add up to {$required[$column]}.");
            }
        }

        return $written;
    }
}
