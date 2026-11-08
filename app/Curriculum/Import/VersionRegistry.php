<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

use App\Enums\CurriculumVersionStatus;
use App\Models\CurriculumVersion;

/**
 * Creates or refreshes a curriculum version by code. A new version starts as draft
 * (D-113); an import never changes the status of an existing one.
 */
final readonly class VersionRegistry
{
    public function __construct(private RowWriter $rows) {}

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

        $version->fill([
            'name' => mb_substr($name, 0, 255),
            'source_file' => $file->relativePath,
            'source_sha256' => $file->sha256,
            'meta' => $meta,
        ]);
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
