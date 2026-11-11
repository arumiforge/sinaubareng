<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Curriculum\Policy\RuleSetDates;
use App\Enums\CurriculumVersionStatus;
use App\Models\CurriculumVersion;

/**
 * Creates or refreshes a curriculum version by code. A new version starts as draft
 * (D-113); an import never changes the status of an existing one. A version can come
 * from several files (SIGRAK-SD-2026-R1: general subjects and religions), so each file keeps its
 * own entry in `meta.files`, and the name is set by the file that creates the version.
 * The version dates come from docs/data/profiles (RuleSetDates, D-355).
 */
final readonly class VersionRegistry
{
    public function __construct(
        private RowWriter $rows,
        private RuleSetDates $dates,
    ) {}

    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function upsert(string $code, string $name, ImportedFile $file, ?array $meta, ImportReport $report, ?string $fileStatus = null): CurriculumVersion
    {
        if (preg_match('/^[A-Z0-9][A-Z0-9-]{2,63}$/', $code) !== 1) {
            throw new InvalidCurriculumFile("Version code '{$code}' must be 3 to 64 capital letters, digits, and dashes.");
        }

        $version = CurriculumVersion::query()->firstOrNew(['code' => $code]);
        $created = ! $version->exists;

        if ($created) {
            $version->status = CurriculumVersionStatus::Draft;
        }

        if ($fileStatus !== null && $fileStatus !== CurriculumVersionStatus::Draft->value) {
            $report->note("The file says status '{$fileStatus}'; the version keeps status '{$version->status->value}' until Tim Validasi SIGRAK signs it off (D-113).");
        }

        if ($created) {
            $version->name = mb_substr($name, 0, 255);
        }

        $files = $version->meta['files'] ?? [];
        $files[$file->relativePath] = ['sha256' => $file->sha256, ...($meta ?? [])];
        ksort($files);
        $version->meta = ['files' => $files];
        // D-355: effective date, first school year, and predecessor from the Profil Jenjang data.
        $this->dates->apply($version);
        $this->rows->ignoreKeyOrder($version);

        $outcome = $created ? 'created' : ($version->isDirty() ? 'updated' : 'unchanged');

        if ($outcome !== 'unchanged' || $version->imported_at === null) {
            $version->imported_at = now();
        }

        $version->save();
        $report->count('curriculum_versions', $outcome);

        return $version;
    }
}
