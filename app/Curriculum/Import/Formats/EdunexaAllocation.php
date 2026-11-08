<?php

declare(strict_types=1);

namespace App\Curriculum\Import\Formats;

use App\Curriculum\Import\AllocationWriter;
use App\Curriculum\Import\CurriculumFormat;
use App\Curriculum\Import\Data;
use App\Curriculum\Import\Grades;
use App\Curriculum\Import\ImportedFile;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\InvalidCurriculumFile;
use App\Curriculum\Import\SubjectLookup;
use App\Curriculum\Import\VersionRegistry;
use App\Enums\LearningType;
use App\Enums\Phase;
use App\Enums\SubjectKind;
use App\Models\CurriculumVersion;
use App\Models\Subject;
use App\Models\SubjectAllocation;

/**
 * EDUNEXA struktur_alokasi_sd.json (KM-SD-2026-R2): `alokasi[]` rows with semester
 * figures, field names from the EDUNEXA mapping doc. Not in the repository yet; the
 * shape follows docs/sources/Pemetaan_Data_Materi_EDUNEXA_untuk_Refactor_RPM_Generator.md
 * and must be checked when the owner supplies the file.
 *
 * `kokurikuler_default[]` (Bahasa Inggris grades 1–2) is not imported: it is optional
 * content a school switches on in school_subjects (D-204). Koding dan KA is not an
 * intrakurikuler allocation (D-208).
 */
final readonly class EdunexaAllocation implements CurriculumFormat
{
    public const DEFAULT_VERSION = 'KM-SD-2026-R2';

    public function __construct(
        private VersionRegistry $versions,
        private AllocationWriter $allocations,
        private SubjectLookup $subjects,
    ) {}

    public function supports(array $json): bool
    {
        return isset($json['alokasi']) && is_array($json['alokasi']);
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $data = $file->data();
        $code = $data->nullableString('versi_kurikulum') ?? $data->nullableString('Versi_Kurikulum') ?? self::DEFAULT_VERSION;

        $version = $this->versions->upsert($code, "{$code}: struktur alokasi SD (EDUNEXA)", $file, array_filter([
            'dataset' => 'struktur_alokasi_sd',
            'generator_rpm' => $data->raw('generator_rpm'),
            'sinkronisasi' => $data->raw('sinkronisasi'),
            'catatan' => $data->raw('catatan'),
        ], fn (mixed $value): bool => $value !== null), $report, $data->nullableString('status_versi'));

        $written = [];

        foreach ($data->objects('alokasi') as $row) {
            $subject = $this->subjects->get($row->string('mata_pelajaran'), $row->path);
            $grade = Grades::grade($row->raw('kelas'), $row->path);
            $phase = Grades::phase($row->string('fase'), $row->path);
            Grades::checkPhase($grade, $phase, $row->path, $report);
            $type = $this->learningType($row);

            if ($subject->kind === SubjectKind::Cocurricular && $type === LearningType::Intracurricular) {
                $report->note("{$subject->name} grade {$grade} is not imported as an intrakurikuler allocation; it runs through kokurikuler (D-208).");

                continue;
            }

            $written[] = $this->write($version, $subject, $grade, $phase, $type, $row, $report)->id;
        }

        foreach ($data->has('kokurikuler_default') ? $data->objects('kokurikuler_default') : [] as $row) {
            $report->note("kokurikuler_default {$row->string('mata_pelajaran')} grade ".json_encode($row->raw('kelas')).' is not imported: optional content that a school switches on (D-204).');
        }

        $this->allocations->reportStale($version, $written, $report);
    }

    private function learningType(Data $row): LearningType
    {
        $value = mb_strtolower($row->nullableString('jenis_pembelajaran') ?? 'intrakurikuler');

        return match ($value) {
            'intrakurikuler', 'intracurricular' => LearningType::Intracurricular,
            'kokurikuler', 'cocurricular' => LearningType::Cocurricular,
            default => throw new InvalidCurriculumFile("{$row->pathTo('jenis_pembelajaran')}: '{$value}' must be Intrakurikuler or Kokurikuler."),
        };
    }

    private function write(CurriculumVersion $version, Subject $subject, int $grade, Phase $phase, LearningType $type, Data $row, ImportReport $report): SubjectAllocation
    {
        $perWeek = $row->int('jp_per_minggu');
        $weeks = $row->int('minggu_tahun');
        $first = $row->object('semester_1');
        $second = $row->object('semester_2');
        $where = "{$subject->code} grade {$grade} {$type->value}";

        if ($first->int('minggu_efektif') + $second->int('minggu_efektif') !== $weeks) {
            throw new InvalidCurriculumFile("{$row->path}: semester weeks do not add up to minggu_tahun {$weeks}.");
        }

        if ($row->has('jp_per_tahun') && $row->int('jp_per_tahun') !== $first->int('jp') + $second->int('jp')) {
            $report->mismatch("{$where}: jp_per_tahun {$row->int('jp_per_tahun')} is not semester 1 + semester 2 ({$first->int('jp')} + {$second->int('jp')}).");
        }

        foreach ([1 => $first, 2 => $second] as $semester => $figures) {
            if ($figures->int('jp') !== $perWeek * $figures->int('minggu_efektif')) {
                $report->mismatch("{$where} semester {$semester}: {$figures->int('jp')} JP is not {$perWeek} JP × {$figures->int('minggu_efektif')} weeks.");
            }
        }

        return $this->allocations->write($version, $subject, $grade, $phase, $type, [
            'jp_per_week' => $perWeek,
            'weeks_per_year' => $weeks,
            's1_jp' => $first->int('jp'),
            's1_effective_weeks' => $first->int('minggu_efektif'),
            's2_jp' => $second->int('jp'),
            's2_effective_weeks' => $second->int('minggu_efektif'),
        ], $report);
    }
}
