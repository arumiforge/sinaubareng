<?php

declare(strict_types=1);

namespace App\Http\Requests\School;

use App\Models\AcademicYear;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Create and update an academic year. Semester 1 runs from `starts_on`, semester 2 from
 * `even_semester_starts_on` to `ends_on`; a year lasts at most twelve months and does
 * not overlap another year of the school.
 */
final class SaveAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        $academicYear = $this->academicYear();

        return $academicYear === null
            ? $this->user()?->can('create', AcademicYear::class) ?? false
            : $this->user()?->can('update', $academicYear) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'even_semester_starts_on' => ['required', 'date_format:Y-m-d', 'after:starts_on'],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after:even_semester_starts_on'],
            // Only on create: copy the previous year's classes (D-242, D-244).
            'copy_classes' => ['sometimes', 'boolean'],
            'with_grade_one' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $startsOn = CarbonImmutable::parse($this->string('starts_on')->toString());
            $endsOn = CarbonImmutable::parse($this->string('ends_on')->toString());

            if ($endsOn->gte($startsOn->addYear())) {
                $validator->errors()->add('ends_on', __('classes.years.errors.too_long'));

                return;
            }

            $others = AcademicYear::query()->when($this->academicYear(), fn ($query, AcademicYear $year) => $query->whereKeyNot($year->id));

            if ((clone $others)->where('name', AcademicYear::nameFor($startsOn))->exists()) {
                $validator->errors()->add('starts_on', __('classes.years.errors.name_taken', ['name' => AcademicYear::nameFor($startsOn)]));
            } elseif ($others->where('starts_on', '<=', $endsOn->toDateString())->where('ends_on', '>=', $startsOn->toDateString())->exists()) {
                $validator->errors()->add('starts_on', __('classes.years.errors.overlap'));
            }
        }];
    }

    private function academicYear(): ?AcademicYear
    {
        $academicYear = $this->route('academicYear');

        return $academicYear instanceof AcademicYear ? $academicYear : null;
    }
}
