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
use App\Enums\LearningType;
use App\Enums\SubjectKind;
use App\Models\CurriculumVersion;
use App\Models\Subject;
use App\Models\SubjectAllocation;

/**
 * EDUNEXA data_kurikulum_dengan_cp.json (KM-SD-2026-R2): one row per topic, with
 * Data_Tujuan, Data_ATP, Data_Fokus, Referensi, and Audit_Data stored as JSON strings.
 * Field names follow the EDUNEXA mapping doc; the file is not in the repository yet,
 * so check this reader when the owner supplies it. `Fokus_Materi` duplicates
 * Data_Fokus and is dropped. Import struktur_alokasi_sd.json first so the semester
 * totals can be checked.
 */
final readonly class EdunexaTopics implements CurriculumFormat
{
    public function __construct(private VersionRegistry $versions, private TopicWriter $topics, private SubjectLookup $subjects) {}

    public function supports(array $json): bool
    {
        $rows = $this->rows($json);

        return $rows !== null && isset($rows[0]['ID_Kurikulum']);
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $rows = [];

        foreach ($this->rows($file->json) ?? [] as $index => $row) {
            $rows[] = new Data($row, "[{$index}]");
        }
        $codes = array_values(array_unique(array_map(fn (Data $row): string => $row->string('Versi_Kurikulum'), $rows)));

        if (count($codes) !== 1) {
            throw new InvalidCurriculumFile('Every row must have the same Versi_Kurikulum; found '.implode(', ', $codes).'.');
        }

        $statuses = array_values(array_unique(array_map(fn (Data $row): string => (string) $row->nullableString('Status_Versi'), $rows)));
        $version = $this->versions->upsert($codes[0], "{$codes[0]}: materi pokok, TP, ATP, fokus (EDUNEXA)", $file, [
            'dataset' => 'data_kurikulum_dengan_cp',
        ], $report, $statuses === ['Aktif'] ? 'active' : null);

        $topics = [];
        $flagged = [];

        foreach ($rows as $row) {
            $topic = $this->topic($row, $codes[0], $report);
            $topics[] = $topic;
            $this->flag($row, $topic, $flagged, $report);
        }

        $this->topics->write($version, $topics, $this->targets($version), $report);
    }

    /**
     * @param  array<array-key, mixed>  $json
     * @return list<array<array-key, mixed>>|null
     */
    private function rows(array $json): ?array
    {
        $rows = array_is_list($json) ? $json : ($json['data'] ?? null);

        return is_array($rows) && array_is_list($rows) && $rows !== [] && is_array($rows[0]) ? $rows : null;
    }

    private function topic(Data $row, string $version, ImportReport $report): TopicInput
    {
        $code = $row->string('ID_Kurikulum');
        $subject = $this->subjects->get($row->string('Mata Pelajaran'), "{$row->path} {$code}");
        $goals = $row->object('Data_Tujuan');
        $atp = $row->object('Data_ATP');
        $texts = $goals->strings('tujuan_pembelajaran');
        $goalCodes = $goals->strings('kode_tp');

        if (count($texts) !== count($goalCodes)) {
            throw new InvalidCurriculumFile("{$code}: Data_Tujuan has ".count($texts).' TP texts and '.count($goalCodes).' kode_tp.');
        }

        $this->checkSubjectCode($code, $version, $subject, $report);
        $references = $row->nullableObject('Referensi');
        $extras = array_filter([
            'art_branch' => $row->raw('Cabang_Seni'),
            'science_process_skills' => $row->raw('Keterampilan_Proses'),
        ], fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []);

        return new TopicInput(
            code: $code,
            legacyCodes: array_values(array_unique(array_filter([
                $row->nullableString('Legacy_ID'),
                ...($row->has('Legacy_ID_Aliases') ? $row->strings('Legacy_ID_Aliases') : []),
            ]))),
            subject: $subject,
            grade: Grades::grade($row->raw('Kelas'), $code),
            phase: Grades::phase($row->string('Fase'), $code),
            semester: $this->semester($row, $code),
            elements: $row->string('Elemen'),
            title: $row->string('Materi Pokok'),
            cpText: $row->string('Capaian Pembelajaran'),
            atpCode: $atp->string('kode_atp'),
            orderInSemester: $atp->int('urutan_semester'),
            jpTotal: $atp->int('alokasi_jp'),
            jpPerWeek: $atp->int('jp_per_minggu'),
            weekStart: $atp->int('minggu_mulai'),
            weekEnd: $atp->int('minggu_selesai'),
            weeklyDistribution: $atp->has('distribusi_mingguan') ? $atp->ints('distribusi_mingguan') : [],
            references: $references === null ? [] : array_filter([
                'cp_source' => $references->raw('sumber_cp'),
                'book' => $references->raw('sumber_buku'),
                'book_pages' => $references->raw('halaman_buku'),
                'adjusted_on' => $references->raw('tanggal_penyesuaian'),
            ], fn (mixed $value): bool => $value !== null),
            audit: $row->nullableObject('Audit_Data')?->toArray(),
            extras: $extras === [] ? null : $extras,
            objectives: array_map(fn (string $text, int $index): array => [
                'code' => $goalCodes[$index],
                'order' => $index + 1,
                'text' => $text,
            ], $texts, array_keys($texts)),
            focusItems: array_map(fn (Data $focus): array => [
                'code' => $focus->string('kode_fokus'),
                'order' => $focus->int('urutan'),
                'title' => $focus->string('fokus'),
                'estimated_jp' => $focus->int('estimasi_jp'),
                'allocation_status' => $focus->nullableString('status_alokasi') ?? '',
                'objective_codes' => $focus->has('kode_tp') ? $focus->strings('kode_tp') : null,
            ], $row->objects('Data_Fokus')),
        );
    }

    private function semester(Data $row, string $code): int
    {
        $value = $row->raw('Semester');
        $semester = match (true) {
            is_int($value) => $value,
            is_string($value) && preg_match('/([12])\)?\s*$/', $value, $match) === 1 => (int) $match[1],
            default => 0,
        };

        if (! in_array($semester, [1, 2], true)) {
            throw new InvalidCurriculumFile("{$code}: Semester '".json_encode($value)."' must be 1, 2, 'Ganjil (1)', or 'Genap (2)'.");
        }

        return $semester;
    }

    /**
     * KM-SD-2026-R2 codes look like <version>-<subject>-K<grade>-<nn>. The catalogue
     * codes other than PP were set before the file was seen (D-227), so a difference
     * is reported for the catalogue to be corrected.
     */
    private function checkSubjectCode(string $code, string $version, Subject $subject, ImportReport $report): void
    {
        if (preg_match('/^'.preg_quote($version, '/').'-(.+)-K\d+-\d+$/', $code, $match) === 1 && $match[1] !== $subject->code) {
            $report->mismatch("Topic {$code} uses subject code {$match[1]}; the catalogue code for {$subject->name} is {$subject->code}.");
        }
    }

    /**
     * @param  array<string, true>  $flagged
     */
    private function flag(Data $row, TopicInput $topic, array &$flagged, ImportReport $report): void
    {
        $key = "{$topic->subject->code}|{$topic->grade}";

        if (isset($flagged[$key])) {
            return;
        }

        $flagged[$key] = true;

        if ($topic->subject->kind === SubjectKind::Cocurricular) {
            $report->note("{$topic->subject->name} grade {$topic->grade}: topics are kept as kokurikuler content (D-208).");
        } elseif ($topic->grade <= 2 && in_array($topic->subject->code, ['BING', 'IPAS'], true)) {
            $report->note("{$topic->subject->name} grade {$topic->grade}: optional content, off until a school switches it on (D-204).");
        } elseif (mb_strtolower((string) $row->nullableString('Jenis_Pembelajaran')) === 'kokurikuler') {
            $report->note("{$topic->subject->name} grade {$topic->grade}: the file marks these topics as kokurikuler.");
        }
    }

    /**
     * @return array<string, int>
     */
    private function targets(CurriculumVersion $version): array
    {
        $targets = [];

        SubjectAllocation::query()->whereBelongsTo($version)->where('learning_type', LearningType::Intracurricular->value)
            ->with('subject')->each(function (SubjectAllocation $allocation) use (&$targets): void {
                $targets["{$allocation->subject->code}|{$allocation->grade}|1"] = $allocation->s1_jp;
                $targets["{$allocation->subject->code}|{$allocation->grade}|2"] = $allocation->s2_jp;
            });

        return $targets;
    }
}
