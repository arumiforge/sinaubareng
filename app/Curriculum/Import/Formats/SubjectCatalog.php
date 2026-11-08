<?php

declare(strict_types=1);

namespace App\Curriculum\Import\Formats;

use App\Curriculum\Import\CurriculumFormat;
use App\Curriculum\Import\ImportedFile;
use App\Curriculum\Import\ImportReport;
use App\Curriculum\Import\InvalidCurriculumFile;
use App\Curriculum\Import\RowWriter;
use App\Curriculum\Import\SubjectLookup;
use App\Enums\DefaultTeacher;
use App\Enums\Religion;
use App\Enums\SubjectKind;
use App\Enums\SubjectSchoolLevel;
use App\Models\Region;
use App\Models\RegionSubject;
use App\Models\Subject;
use BackedEnum;

/**
 * docs/data/curriculum/subjects.json: the subject catalogue and the regional local
 * content defaults (D-227). Every other file names its subjects against it.
 */
final readonly class SubjectCatalog implements CurriculumFormat
{
    public function __construct(private RowWriter $rows, private SubjectLookup $subjects) {}

    public function supports(array $json): bool
    {
        return ($json['dataset'] ?? null) === 'subjects';
    }

    public function import(ImportedFile $file, ImportReport $report): void
    {
        $data = $file->data();
        $codes = [];
        $names = [];

        foreach ($data->objects('subjects') as $row) {
            $code = $row->string('code');

            if (preg_match('/^[A-Z][A-Z0-9]{1,15}$/', $code) !== 1) {
                throw new InvalidCurriculumFile("{$row->pathTo('code')}: '{$code}' must be 2 to 16 capital letters and digits.");
            }

            $aliases = $row->has('aliases') ? $row->strings('aliases') : [];

            foreach ([$code, $row->string('name'), ...$aliases] as $name) {
                $key = SubjectLookup::normalize($name);

                if (isset($names[$key]) && $names[$key] !== $code) {
                    throw new InvalidCurriculumFile("{$row->path}: '{$name}' already names subject {$names[$key]}.");
                }

                $names[$key] = $code;
            }

            if (isset($codes[$code])) {
                throw new InvalidCurriculumFile("{$row->path}: subject code {$code} appears twice.");
            }

            $codes[$code] = true;

            $religion = $row->nullableString('religion');

            $this->rows->upsert(Subject::class, ['code' => $code], [
                'name' => $row->string('name'),
                'kind' => $this->enum(SubjectKind::class, $row->string('kind'), $row->pathTo('kind')),
                'default_teacher' => $this->enum(DefaultTeacher::class, $row->string('default_teacher'), $row->pathTo('default_teacher')),
                'religion' => $religion === null ? null : $this->enum(Religion::class, $religion, $row->pathTo('religion')),
                'school_level' => $this->enum(SubjectSchoolLevel::class, $row->string('school_level'), $row->pathTo('school_level')),
                'aliases' => $aliases,
            ], $report);
        }

        $this->subjects->refresh();

        foreach (Subject::query()->whereNotIn('code', array_keys($codes))->pluck('code') as $missing) {
            $report->mismatch("Subject {$missing} is in the database but not in the file; it is kept.");
        }

        foreach ($data->has('region_defaults') ? $data->objects('region_defaults') : [] as $row) {
            $region = Region::query()->firstOrCreate(['province' => $row->string('province'), 'regency' => $row->string('regency')]);
            $allocation = $row->int('allocation');

            if ($allocation < 1 || $allocation > 6) {
                throw new InvalidCurriculumFile("{$row->pathTo('allocation')} must be 1–6 JP per week.");
            }

            $this->rows->upsert(RegionSubject::class, [
                'region_id' => $region->id,
                'subject_id' => $this->subjects->get($row->string('subject'), $row->path)->id,
            ], ['allocation' => $allocation], $report);
        }
    }

    /**
     * @template T of BackedEnum
     *
     * @param  class-string<T>  $enum
     */
    private function enum(string $enum, string $value, string $where): string
    {
        if ($enum::tryFrom($value) === null) {
            $allowed = implode(', ', array_map(fn (BackedEnum $case): string => (string) $case->value, $enum::cases()));

            throw new InvalidCurriculumFile("{$where}: '{$value}' must be one of {$allowed}.");
        }

        return $value;
    }
}
