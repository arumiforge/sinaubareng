<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\NationalHolidayKind;
use App\Models\NationalHoliday;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Loads national holidays and cuti bersama from a file such as
 * docs/data/calendar/national-holidays-2026-2027.json (D-253). Idempotent per date: a
 * date already present is updated, other dates are left as they are.
 */
#[Signature('calendar:import-holidays {file : JSON file, e.g. docs/data/calendar/national-holidays-2026-2027.json}')]
#[Description('Import national holidays and cuti bersama')]
final class ImportNationalHolidays extends Command
{
    public function handle(): int
    {
        $path = (string) $this->argument('file');
        $data = is_file($path) ? json_decode((string) file_get_contents($path), true) : null;

        $validator = Validator::make(is_array($data) ? $data : [], [
            'dataset' => ['required', Rule::in(['national_holidays'])],
            'academic_years' => ['required', 'array', 'min:1'],
            'academic_years.*.academic_year' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'academic_years.*.days' => ['required', 'array'],
            'academic_years.*.days.*.date' => ['required', 'date_format:Y-m-d'],
            'academic_years.*.days.*.name' => ['required', 'string', 'max:255'],
            'academic_years.*.days.*.kind' => ['required', Rule::enum(NationalHolidayKind::class)],
            'academic_years.*.days.*.decree' => ['nullable', 'string', 'max:255'],
            'academic_years.*.days.*.is_provisional' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            foreach (array_slice($validator->errors()->all(), 0, 10) as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        /** @var array{academic_years: list<array{academic_year: string, days: list<array{date: string, name: string, kind: string, decree?: string|null, is_provisional?: bool}>}>} $valid */
        $valid = $validator->validated();
        $dates = collect($valid['academic_years'])->flatMap(fn (array $year): array => array_column($year['days'], 'date'));

        if ($dates->duplicates()->isNotEmpty()) {
            $this->error('Tanggal ganda: '.$dates->duplicates()->implode(', '));

            return self::FAILURE;
        }

        $counts = DB::transaction(function () use ($valid): array {
            $counts = ['created' => 0, 'updated' => 0, 'unchanged' => 0];

            foreach ($valid['academic_years'] as $year) {
                foreach ($year['days'] as $day) {
                    $row = NationalHoliday::query()->firstOrNew(['date' => $day['date']]);
                    $row->fill([
                        'name' => $day['name'],
                        'kind' => $day['kind'],
                        'decree' => $day['decree'] ?? null,
                        'academic_year' => $year['academic_year'],
                        'is_provisional' => $day['is_provisional'] ?? false,
                    ]);

                    $key = ! $row->exists ? 'created' : ($row->isDirty() ? 'updated' : 'unchanged');
                    $row->save();
                    $counts[$key]++;
                }
            }

            return $counts;
        });

        $this->info("Libur nasional: {$counts['created']} baru, {$counts['updated']} diubah, {$counts['unchanged']} tetap.");

        return self::SUCCESS;
    }
}
