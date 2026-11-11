<?php

declare(strict_types=1);

namespace App\Http\Requests\School;

use App\Actions\Timetable\TimetableRules;
use App\Curriculum\SubjectOffer;
use App\Enums\TimetableSlotKind;
use App\Models\SchoolClass;
use App\Models\TimetableSlot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Create or change one row of a class's timetable; the rules are TimetableRules
 * (D-247…D-251), shared with saving a draft (D-262).
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

            $offer = null;
            $broken = app(TimetableRules::class)->check($this->schoolClass(), [
                'weekday' => $this->integer('weekday'),
                'starts_at' => $this->time('starts_at'),
                'ends_at' => $this->time('ends_at'),
                'kind' => $this->string('kind')->toString(),
                'subject' => $this->input('kind') === TimetableSlotKind::Lesson->value ? $this->string('subject')->toString() : null,
                'jp' => $this->integer('jp'),
            ], $this->slot(), $offer);
            $this->offer = $offer;

            if ($broken !== null) {
                $validator->errors()->add(...$broken);
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
