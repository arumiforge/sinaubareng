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
use App\Enums\LearningType;
use App\Enums\SubjectKind;
use App\Models\CurriculumVersion;
use App\Models\Subject;
use App\Models\SubjectAllocation;

/**
 * EDUNEXA data_kurikulum_dengan_cp.json (KM-SD-2026-R2): one row per topic, with
 * Data_Tujuan, Data_ATP, Data_Fokus, Referensi, and Audit_Data as JSON objects (or JSON
 * strings in older exports). Findings on the shape: docs/drafts/km-sd-2026-r2-source-check.md.
 * `Fokus_Materi` duplicates Data_Fokus and is dropped. Data_ATP keys without a column,
 * Status_Mapel, Jenis_Pembelajaran, and the art branch go to `extras`. Import
 * struktur_alokasi_sd.json first so the semester totals can be checked. Owner-approved
 * corrections (SourceOverrides, D-234) are applied after a row is read.
 */
final readonly class EdunexaTopics implements CurriculumFormat
{
    /** Data_ATP keys stored in topic columns. */
    private const ATP_COLUMNS = ['kode_atp', 'urutan_semester', 'alokasi_jp', 'jp_per_minggu', 'minggu_mulai', 'minggu_selesai', 'distribusi_mingguan'];

    private const REFERENCE_KEYS = ['sumber_cp' => 'cp_source', 'sumber_buku' => 'book', 'halaman_buku' => 'book_pages', 'tanggal_penyesuaian' => 'adjusted_on'];

    public function __construct(private VersionRegistry $versions, private TopicWriter $topics, private SubjectLookup $subjects, private SourceOverrides $overrides) {}

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
            $topic = $this->overrides->apply($this->topic($row, $codes[0], $report), $report);
            $topics[] = $topic;
            $this->flag($row, $topic, $flagged, $report);
        }

        $this->reportAmbiguousAliases($topics, $report);
        $this->topics->write($version, $topics, $this->targets($version), $report);
    }

    /**
     * Legacy codes are a fallback lookup only: look up topics.code first. Aliases that
     * name several topics, or another topic's current code, cannot be resolved.
     *
     * @param  list<TopicInput>  $topics
     */
    private function reportAmbiguousAliases(array $topics, ImportReport $report): void
    {
        $owners = [];
        $codes = array_flip(array_map(fn (TopicInput $topic): string => $topic->code, $topics));

        foreach ($topics as $topic) {
            foreach ($topic->legacyCodes as $legacy) {
                $owners[$legacy][] = $topic->code;
            }
        }

        $shared = array_filter($owners, fn (array $rows): bool => count($rows) > 1);
        $current = array_intersect_key($owners, $codes);

        if ($shared !== []) {
            $report->note(count($shared).' legacy code(s) belong to several topics and cannot be resolved, e.g. '.array_key_first($shared).' ('.implode(', ', reset($shared)).').');
        }

        if ($current !== []) {
            $report->note(count($current).' legacy code(s) are the current code of another topic; a lookup uses topics.code first, e.g. '.array_key_first($current).' (alias of '.implode(', ', reset($current)).').');
        }
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
            'subject_status' => $row->raw('Status_Mapel'),
            'learning_type' => $row->raw('Jenis_Pembelajaran'),
            'atp' => array_diff_key($atp->toArray(), array_flip(self::ATP_COLUMNS)),
        ], fn (mixed $value): bool => $value !== null && $value !== '' && $value !== []);

        return new TopicInput(
            code: $code,
            legacyCodes: array_values(array_diff(array_unique(array_filter([
                $row->nullableString('Legacy_ID'),
                ...($row->has('Legacy_ID_Aliases') ? $row->strings('Legacy_ID_Aliases') : []),
            ])), [$code])),
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
            weeklyDistribution: $this->weeklyDistribution($atp, $code),
            references: $references === null ? [] : $this->references($references),
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

    /**
     * A list of JP per week, or of {minggu, jp} objects in week order.
     *
     * @return list<int>
     */
    private function weeklyDistribution(Data $atp, string $code): array
    {
        if (! $atp->has('distribusi_mingguan')) {
            return [];
        }

        $weeks = $atp->list('distribusi_mingguan');

        if ($weeks === [] || ! is_array($weeks[0])) {
            return $atp->ints('distribusi_mingguan');
        }

        $jp = [];
        $start = $atp->int('minggu_mulai');

        foreach ($atp->objects('distribusi_mingguan') as $index => $week) {
            if ($week->int('minggu') !== $start + $index) {
                throw new InvalidCurriculumFile("{$code}: distribusi_mingguan must list the weeks in order from minggu_mulai {$start}.");
            }

            $jp[] = $week->int('jp');
        }

        return $jp;
    }

    /**
     * The whole Referensi object, with the four keys the RPM uses under the English
     * names the other topic files use.
     *
     * @return array<string, mixed>
     */
    private function references(Data $references): array
    {
        $values = $references->toArray();
        $renamed = [];

        foreach (self::REFERENCE_KEYS as $source => $target) {
            if (array_key_exists($source, $values)) {
                $renamed[$target] = $values[$source];
                unset($values[$source]);
            }
        }

        /** @var array<string, mixed> */
        return [...$renamed, ...$values];
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
     * KM-SD-2026-R2 codes look like <version>-<subject>-K<grade>-<nn>. The subject part
     * must be the catalogue code or an alias of the same subject (Seni dan Budaya uses
     * five), so a new code is reported for the catalogue to be corrected.
     */
    private function checkSubjectCode(string $code, string $version, Subject $subject, ImportReport $report): void
    {
        if (preg_match('/^'.preg_quote($version, '/').'-(.+)-K\d+-\d+$/', $code, $match) === 1 && $this->subjects->find($match[1])?->is($subject) !== true) {
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
        } elseif ($topic->grade <= 2 && in_array($topic->subject->code, ['BIG', 'IPAS'], true)) {
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
