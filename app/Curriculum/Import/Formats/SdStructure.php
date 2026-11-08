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
use App\Models\Subject;

/**
 * docs/data/regulations/curriculum-structure-permendikdasmen-13-2025.json, Tabel 1–5
 * (`jp_allocation_sd_mi`): SD intrakurikuler and kokurikuler JP per subject and grade.
 * Pendidikan Agama is one row for six religions and fills each religion subject. The
 * muatan lokal row is a cap, kept in the version's meta, not an allocation. Koding dan
 * KA is not an intrakurikuler subject in SIGRAK (D-208). Only this part of the file
 * belongs in the curriculum tables; its other sections are for later slices.
 */
final readonly class SdStructure implements CurriculumFormat
{
    public function __construct(
        private VersionRegistry $versions,
        private AllocationWriter $allocations,
        private SubjectLookup $subjects,
    ) {}

    public function supports(array $json): bool
    {
        return ($json['dataset'] ?? null) === 'curriculum_structure_sd';
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $data = $file->data();
        $structure = $data->object('jp_allocation_sd_mi');
        $tables = $structure->objects('tables');
        $localContentCaps = [];

        foreach ($tables as $table) {
            foreach ($table->objects('subjects') as $row) {
                if ($row->string('group') === 'muatan_lokal') {
                    foreach ($table->list('grades') as $grade) {
                        $localContentCaps[(string) Grades::grade($grade, $table->path)] = $row->int('intrakurikuler');
                    }
                }
            }
        }

        $source = $data->object('source');
        $version = $this->versions->upsert($data->string('version_code'), $source->string('title'), $file, [
            'dataset' => $data->string('dataset'),
            'source' => $source->only(['title', 'number', 'issued_on', 'file', 'sha256']),
            'units' => $structure->raw('units'),
            'local_content_max_jp_per_year' => $localContentCaps,
            'imported_part' => 'jp_allocation_sd_mi (Lampiran II B, Tabel 1–5)',
        ], $report, $data->nullableString('status'));

        $written = [];

        foreach ($tables as $table) {
            $written = [...$written, ...$this->importTable($version, $table, $report)];
        }

        $this->allocations->reportStale($version, $written, $report);
        $report->note('Only Lampiran II B, Tabel 1–5 is imported. The other sections of the file (rules, SDLB/MILB tables, ekstrakurikuler guidance) stay in the file for later slices.');
    }

    /**
     * @return list<int> the allocation ids written
     */
    private function importTable(CurriculumVersion $version, Data $table, ImportReport $report): array
    {
        $phase = Grades::phase($table->string('phase'), $table->path);
        $weeks = $table->int('weeks_per_year');
        $where = 'Tabel '.$table->int('table');
        $sum = ['intrakurikuler' => 0, 'kokurikuler' => 0];
        $written = [];

        foreach ($table->objects('subjects') as $row) {
            $intra = $row->int('intrakurikuler');
            $co = $row->int('kokurikuler');

            if ($row->has('total') && $row->int('total') !== $intra + $co) {
                $report->mismatch("{$where} {$row->string('name')}: total {$row->int('total')} is not intrakurikuler {$intra} + kokurikuler {$co}.");
            }

            if ($row->string('group') === 'required') {
                $sum['intrakurikuler'] += $intra;
                $sum['kokurikuler'] += $co;
            }

            if ($row->string('group') === 'muatan_lokal') {
                continue;
            }

            foreach ($this->subjectsOf($row, $where) as $subject) {
                if ($subject->kind === SubjectKind::Cocurricular) {
                    $report->note("{$where}: {$subject->name} ({$intra} JP per year, elective) is not imported as an intrakurikuler allocation; it runs through kokurikuler (D-208).");

                    continue;
                }

                foreach ($table->list('grades') as $gradeValue) {
                    $grade = Grades::grade($gradeValue, $where);
                    Grades::checkPhase($grade, $phase, $where, $report);

                    foreach ([LearningType::Intracurricular->value => $intra, LearningType::Cocurricular->value => $co] as $type => $jp) {
                        $row = $jp > 0 ? $this->allocations->writeYearly($version, $subject, $grade, $phase, LearningType::from($type), $jp, $weeks, $report) : null;

                        if ($row !== null) {
                            $written[] = $row->id;
                        }
                    }
                }
            }
        }

        $this->checkPrintedTotal($table, $where, $sum, $report);

        return $written;
    }

    /**
     * @return list<Subject>
     */
    private function subjectsOf(Data $row, string $where): array
    {
        if ($row->has('variants')) {
            return array_map(fn (string $name): Subject => $this->subjects->get($name, $where), $row->strings('variants'));
        }

        return [$this->subjects->get($row->string('key'), $where)];
    }

    /**
     * @param  array{intrakurikuler: int, kokurikuler: int}  $sum
     */
    private function checkPrintedTotal(Data $table, string $where, array $sum, ImportReport $report): void
    {
        foreach ($table->objects('totals_printed') as $total) {
            if ($total->string('label') !== 'Total JP Mata Pelajaran Wajib') {
                continue;
            }

            foreach (['intrakurikuler', 'kokurikuler'] as $column) {
                if ($total->int($column) !== $sum[$column]) {
                    $report->mismatch("{$where}: printed {$column} total for Mata Pelajaran Wajib is {$total->int($column)}, the rows add up to {$sum[$column]}.");
                }
            }
        }
    }
}
