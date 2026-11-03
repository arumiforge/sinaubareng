<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SchoolLevel;
use App\Models\Region;
use App\Models\SchoolDirectoryEntry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Loads a file written by scripts/school-directory/fetch.cjs. Idempotent by NPSN; a school
 * missing from a newer file of the same regency is removed from the directory.
 */
#[Signature('school-directory:import {file : JSON file, e.g. docs/data/schools/kudus.json}')]
#[Description('Import SD/MI reference data for one regency')]
final class ImportSchoolDirectory extends Command
{
    public function handle(): int
    {
        $path = (string) $this->argument('file');
        $data = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;

        $validator = Validator::make(is_array($data) ? $data : [], [
            'fetched_on' => ['required', 'date_format:Y-m-d'],
            'province' => ['required', 'string', 'max:255'],
            'regency' => ['required', 'string', 'max:255'],
            'schools' => ['required', 'array', 'min:1'],
            'schools.*.npsn' => ['required', 'regex:/^\d{8}$/', 'distinct'],
            'schools.*.name' => ['required', 'string', 'max:255'],
            'schools.*.level' => ['required', Rule::enum(SchoolLevel::class)],
            'schools.*.address' => ['present', 'string', 'max:255'],
            'schools.*.village' => ['present', 'string', 'max:255'],
            'schools.*.district' => ['required', 'string', 'max:255'],
            // Optional: added by `fetch.cjs --coordinates`, null when the source has no location.
            'schools.*.latitude' => ['nullable', 'numeric', 'between:-11.5,6.5', 'required_with:schools.*.longitude'],
            'schools.*.longitude' => ['nullable', 'numeric', 'between:94,141.5', 'required_with:schools.*.latitude'],
        ]);

        if ($validator->fails()) {
            foreach (array_slice($validator->errors()->all(), 0, 10) as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        /** @var array{fetched_on: string, province: string, regency: string, schools: list<array{npsn: string, name: string, level: string, address: string, village: string, district: string, latitude?: float|null, longitude?: float|null}>} $valid */
        $valid = $validator->validated();

        $count = DB::transaction(function () use ($valid): int {
            $region = Region::query()->firstOrCreate(['province' => $valid['province'], 'regency' => $valid['regency']]);

            $rows = array_map(fn (array $school): array => [
                ...$school,
                'latitude' => $school['latitude'] ?? null,
                'longitude' => $school['longitude'] ?? null,
                'region_id' => $region->id,
                'fetched_on' => $valid['fetched_on'],
            ], $valid['schools']);

            foreach (array_chunk($rows, 500) as $chunk) {
                SchoolDirectoryEntry::query()->upsert($chunk, ['npsn'], ['name', 'level', 'address', 'village', 'district', 'region_id', 'latitude', 'longitude', 'fetched_on']);
            }

            SchoolDirectoryEntry::query()
                ->whereBelongsTo($region)
                ->whereNotIn('npsn', array_column($rows, 'npsn'))
                ->delete();

            return count($rows);
        });

        $this->info("{$count} schools imported for {$valid['regency']}.");

        return self::SUCCESS;
    }
}
