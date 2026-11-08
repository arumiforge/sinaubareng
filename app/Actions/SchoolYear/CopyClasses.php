<?php

declare(strict_types=1);

namespace App\Actions\SchoolYear;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Copies the previous year's classes into a new, empty academic year (D-242…D-246):
 *
 * - every class moves up one grade; grade 6 is not copied (D-243);
 * - a name that starts with the grade number follows it (4A → 5A), other names stay (D-245);
 * - optionally, last year's grade 1 classes are made again with the same names (D-244);
 * - homeroom and subject teachers stay empty (no teaching assignment rows, D-240), and no
 *   students move: Kenaikan comes with the student slice;
 * - the target year must have no classes (D-246).
 *
 * Both years come through the school scope, so only the school in the context is touched.
 */
final class CopyClasses
{
    private const int NAME_LIMIT = 20;

    /** The school's latest year that starts before the target year (D-246). */
    public function sourceFor(AcademicYear $target): ?AcademicYear
    {
        return AcademicYear::query()
            ->where('starts_on', '<', $target->starts_on->toDateString())
            ->orderByDesc('starts_on')
            ->first();
    }

    /**
     * The classes a copy from `$source` would make, without saving.
     *
     * @return list<array{grade: int, name: string, new_intake: bool}>
     */
    public function plan(AcademicYear $source, bool $withGradeOne = true): array
    {
        $classes = $source->classes()->orderBy('grade')->orderBy('name')->get(['grade', 'name']);
        $used = [];
        $plan = [];

        foreach ($classes as $class) {
            if ($class->grade >= 6) {
                continue;
            }

            $grade = $class->grade + 1;
            $plan[] = ['grade' => $grade, 'name' => $this->uniqueName($this->moveUp($class->name, $class->grade), $grade, $used), 'new_intake' => false];
        }

        if ($withGradeOne) {
            foreach ($classes->where('grade', 1) as $class) {
                $plan[] = ['grade' => 1, 'name' => $this->uniqueName($class->name, 1, $used), 'new_intake' => true];
            }
        }

        return $plan;
    }

    /**
     * @return Collection<int, SchoolClass>
     *
     * @throws CopyClassesRefused
     */
    public function handle(AcademicYear $target, bool $withGradeOne): Collection
    {
        return DB::transaction(function () use ($target, $withGradeOne): Collection {
            // Lock the target year so two copies at the same time cannot both pass the empty check.
            AcademicYear::query()->whereKey($target->id)->lockForUpdate()->first();

            if ($target->classes()->exists()) {
                throw new CopyClassesRefused(__('classes.copy.errors.not_empty', ['name' => $target->name]));
            }

            $source = $this->sourceFor($target);
            $plan = $source === null ? [] : $this->plan($source, $withGradeOne);

            if ($plan === []) {
                throw new CopyClassesRefused(__('classes.copy.errors.nothing'));
            }

            return collect($plan)->map(fn (array $row): SchoolClass => $target->classes()->create([
                'grade' => $row['grade'],
                'name' => $row['name'],
                'homeroom_teacher_id' => null,
            ]));
        });
    }

    /** `4A`, `4 B`, `4-Umar` become `5A`, `5 B`, `5-Umar`; `Kelas Merah` and `40A` stay (D-245). */
    private function moveUp(string $name, int $grade): string
    {
        return preg_replace('/^'.$grade.'(?!\d)/u', (string) ($grade + 1), $name) ?? $name;
    }

    /**
     * A name not used yet in the new year; a clash gets the grade added, cut to the limit (D-245).
     *
     * @param  array<string, true>  $used  lower-cased names already taken
     */
    private function uniqueName(string $name, int $grade, array &$used): string
    {
        $candidate = $name;

        for ($attempt = 1; isset($used[mb_strtolower($candidate)]); $attempt++) {
            $suffix = ' '.$grade.($attempt > 1 ? '-'.$attempt : '');
            $candidate = rtrim(mb_substr($name, 0, self::NAME_LIMIT - mb_strlen($suffix))).$suffix;
        }

        $used[mb_strtolower($candidate)] = true;

        return $candidate;
    }
}
