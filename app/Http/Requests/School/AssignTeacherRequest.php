<?php

declare(strict_types=1);

namespace App\Http\Requests\School;

use App\Actions\SchoolYear\ClassSubjects;
use App\Actions\SchoolYear\TeacherOptions;
use App\Actions\Timetable\TimetableClashes;
use App\Curriculum\SubjectOffer;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * The operator picks a subject's teacher in a class, or clears it with an empty teacher.
 * The subject must be one the class takes; the teacher an active teacher of the school.
 */
final class AssignTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->schoolClass()) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:20'],
            'teacher' => ['nullable', 'uuid'],
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

            if ($this->subject() === null) {
                $validator->errors()->add('subject', __('classes.assignments.errors.subject_missing'));
            }

            if ($this->filled('teacher') && $this->teacherId() === null) {
                $validator->errors()->add('teacher', __('classes.classes.errors.teacher_missing'));

                return;
            }

            // The new teacher may not have a lesson elsewhere at this subject's times (D-248).
            $teacherId = $this->teacherId();
            $subject = $this->subject();

            if ($teacherId !== null && $subject !== null) {
                $clashes = app(TimetableClashes::class);
                $clash = $clashes->forSubject($teacherId, $this->schoolClass(), $subject->id);

                if ($clash !== null) {
                    $validator->errors()->add('teacher', $clashes->message($clash, (string) User::query()->whereKey($teacherId)->value('name')));
                }
            }
        }];
    }

    /**
     * A subject the class takes, by its catalogue code. A subject the class no longer
     * takes (the school switched it off) can still be cleared.
     */
    public function subject(): ?Subject
    {
        $code = $this->string('subject')->toString();
        $offered = app(ClassSubjects::class)->handle($this->schoolClass())
            ->first(fn (SubjectOffer $offer): bool => $offer->subject->code === $code)
            ?->subject;

        if ($offered !== null || $this->filled('teacher')) {
            return $offered;
        }

        return $this->schoolClass()->teachingAssignments()->with('subject')->get()
            ->first(fn (TeachingAssignment $assignment): bool => $assignment->subject->code === $code)
            ?->subject;
    }

    public function teacherId(): ?int
    {
        return app(TeacherOptions::class)->userId($this->filled('teacher') ? $this->string('teacher')->toString() : null);
    }

    private function schoolClass(): SchoolClass
    {
        $class = $this->route('class');

        return $class instanceof SchoolClass ? $class : abort(404);
    }
}
