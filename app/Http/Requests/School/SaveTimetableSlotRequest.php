<?php

declare(strict_types=1);

namespace App\Http\Requests\School;

use App\Actions\SchoolYear\ClassSubjects;
use App\Actions\Timetable\TimetableClashes;
use App\Curriculum\SubjectOffer;
use App\Enums\TimetableSlotKind;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\TimetableSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Create or change one row of a class's timetable (D-247…D-250):
 *
 * - rows of a class may not overlap on a day, except lessons of different religions (D-251);
 * - a lesson needs a subject the class takes that already has a teacher (D-249);
 * - a subject may not go over its weekly JP allocation (D-250);
 * - the teacher may not have a lesson in another class at the same time (D-248).
 */
final class SaveTimetableSlotRequest extends FormRequest
{
    private ?SubjectOffer $offer = null;

    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->schoolClass()) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $lesson = $this->input('kind') === TimetableSlotKind::Lesson->value;

        return [
            'weekday' => ['required', 'integer', 'between:1,6'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],
            'kind' => ['required', Rule::enum(TimetableSlotKind::class)],
            'subject' => $lesson ? ['required', 'string', 'max:20'] : ['nullable', 'prohibited'],
            'jp' => $lesson ? ['required', 'integer', 'between:1,4'] : ['nullable', 'prohibited'],
            'label' => $lesson ? ['nullable', 'prohibited'] : ['nullable', 'string', 'max:40'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'weekday' => __('timetable.fields.weekday'),
            'starts_at' => __('timetable.fields.starts_at'),
            'ends_at' => __('timetable.fields.ends_at'),
            'kind' => __('timetable.fields.kind'),
            'subject' => __('timetable.fields.subject'),
            'jp' => __('timetable.fields.jp'),
            'label' => __('timetable.fields.label'),
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

            $class = $this->schoolClass();
            [$weekday, $startsAt, $endsAt] = [$this->integer('weekday'), $this->time('starts_at'), $this->time('ends_at')];

            $overlapping = $class->timetableSlots()
                ->when($this->slot(), fn ($query, TimetableSlot $slot) => $query->whereKeyNot($slot->id))
                ->where('weekday', $weekday)
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->with('subject:id,code,religion')
                ->get();

            // D-251: lessons of different religion subjects may share a time; nothing else may overlap.
            $code = $this->input('kind') === TimetableSlotKind::Lesson->value ? $this->string('subject')->toString() : null;
            $religionOnly = $code !== null
                && Subject::query()->where('code', $code)->whereNotNull('religion')->exists()
                && $overlapping->every(fn (TimetableSlot $other): bool => $other->subject?->religion !== null && $other->subject->code !== $code);

            if ($overlapping->isNotEmpty() && ! $religionOnly) {
                $validator->errors()->add('starts_at', __('timetable.errors.overlap'));

                return;
            }

            if ($this->input('kind') !== TimetableSlotKind::Lesson->value) {
                return;
            }

            $code = $this->string('subject')->toString();
            $this->offer = app(ClassSubjects::class)->handle($class)->first(fn (SubjectOffer $offer): bool => $offer->subject->code === $code);

            if ($this->offer === null) {
                $validator->errors()->add('subject', __('classes.assignments.errors.subject_missing'));

                return;
            }

            $subject = $this->offer->subject;
            $assignment = TeachingAssignment::query()->where('class_id', $class->id)->where('subject_id', $subject->id)->with('teacher:id,name')->first();

            if ($assignment === null) {
                $validator->errors()->add('subject', __('timetable.errors.no_teacher', ['subject' => $subject->name]));

                return;
            }

            if ($this->offer->jpPerWeek !== null) {
                $scheduled = (int) $class->timetableSlots()
                    ->when($this->slot(), fn ($query, TimetableSlot $slot) => $query->whereKeyNot($slot->id))
                    ->where('subject_id', $subject->id)
                    ->sum('jp');

                if ($scheduled + $this->integer('jp') > $this->offer->jpPerWeek) {
                    $validator->errors()->add('jp', __('timetable.errors.over_allocation', [
                        'subject' => $subject->name,
                        'allocated' => $this->offer->jpPerWeek,
                        'left' => max(0, $this->offer->jpPerWeek - $scheduled),
                    ]));

                    return;
                }
            }

            $clashes = app(TimetableClashes::class);
            $clash = $clashes->find($assignment->teacher_id, $class, $weekday, $startsAt, $endsAt);

            if ($clash !== null) {
                $validator->errors()->add('starts_at', $clashes->message($clash, $assignment->teacher->name));
            }
        }];
    }

    /**
     * @return array{weekday: int, starts_at: string, ends_at: string, kind: string, subject_id: int|null, label: string|null, jp: int}
     */
    public function slotData(): array
    {
        $lesson = $this->input('kind') === TimetableSlotKind::Lesson->value;
        $label = $this->string('label')->trim()->toString();

        return [
            'weekday' => $this->integer('weekday'),
            'starts_at' => $this->time('starts_at'),
            'ends_at' => $this->time('ends_at'),
            'kind' => $this->string('kind')->toString(),
            'subject_id' => $lesson ? $this->offer?->subject->id : null,
            'label' => $lesson || $label === '' ? null : $label,
            'jp' => $lesson ? $this->integer('jp') : 0,
        ];
    }

    private function time(string $key): string
    {
        return $this->string($key)->toString().':00';
    }

    private function schoolClass(): SchoolClass
    {
        $class = $this->route('class');

        return $class instanceof SchoolClass ? $class : abort(404);
    }

    private function slot(): ?TimetableSlot
    {
        $slot = $this->route('timetableSlot');

        return $slot instanceof TimetableSlot ? $slot : null;
    }
}
