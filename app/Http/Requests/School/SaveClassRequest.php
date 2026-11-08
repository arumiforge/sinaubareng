<?php

declare(strict_types=1);

namespace App\Http\Requests\School;

use App\Actions\SchoolYear\TeacherOptions;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Create and update a class. The academic year is chosen on create only; the homeroom
 * teacher is an active teacher of the school, named by membership public id.
 */
final class SaveClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        $class = $this->schoolClass();

        return $class === null
            ? $this->user()?->can('create', SchoolClass::class) ?? false
            : $this->user()?->can('update', $class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'academic_year' => $this->schoolClass() === null ? ['required', 'uuid'] : ['prohibited'],
            'grade' => ['required', 'integer', 'between:1,6'],
            'name' => ['required', 'string', 'max:20'],
            'homeroom_teacher' => ['nullable', 'uuid'],
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

            $yearId = $this->academicYearId();

            if ($yearId === null) {
                $validator->errors()->add('academic_year', __('classes.classes.errors.year_missing'));

                return;
            }

            if ($this->filled('homeroom_teacher') && $this->homeroomTeacherId() === null) {
                $validator->errors()->add('homeroom_teacher', __('classes.classes.errors.teacher_missing'));
            }

            $nameTaken = SchoolClass::query()
                ->where('academic_year_id', $yearId)
                ->where('name', $this->name())
                ->when($this->schoolClass(), fn ($query, SchoolClass $class) => $query->whereKeyNot($class->id))
                ->exists();

            if ($nameTaken) {
                $validator->errors()->add('name', __('classes.classes.errors.name_taken'));
            }
        }];
    }

    /**
     * @return array{academic_year_id?: int, grade: int, name: string, homeroom_teacher_id: int|null}
     */
    public function classData(): array
    {
        $data = [
            'grade' => $this->integer('grade'),
            'name' => $this->name(),
            'homeroom_teacher_id' => $this->homeroomTeacherId(),
        ];

        if ($this->schoolClass() === null) {
            $data['academic_year_id'] = (int) $this->academicYearId();
        }

        return $data;
    }

    private function name(): string
    {
        return $this->string('name')->squish()->toString();
    }

    private function academicYearId(): ?int
    {
        $class = $this->schoolClass();

        if ($class !== null) {
            return $class->academic_year_id;
        }

        // Scoped to the current school, so another school's year is "missing" (TEN-4).
        return AcademicYear::query()->where('public_id', $this->string('academic_year')->toString())->value('id');
    }

    private function homeroomTeacherId(): ?int
    {
        return app(TeacherOptions::class)->userId($this->filled('homeroom_teacher') ? $this->string('homeroom_teacher')->toString() : null);
    }

    private function schoolClass(): ?SchoolClass
    {
        $class = $this->route('class');

        return $class instanceof SchoolClass ? $class : null;
    }
}
