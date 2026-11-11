<?php

declare(strict_types=1);

namespace App\Http\Requests\School;

use App\Calendar\Conflict;
use App\Calendar\ConflictChecker;
use App\Enums\CalendarEventKind;
use App\Enums\ExamType;
use App\Models\AcademicYear;
use App\Models\CalendarEvent;
use App\Models\SchoolCalendar;
use App\Models\SchoolClass;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Create or change a calendar event (D-258, D-260). The class, when given, must be a
 * class of the calendar's year; a wali kelas may only pick their own class. Conflict
 * errors refuse the save; conflict warnings are passed on to the toast.
 */
final class SaveCalendarEventRequest extends FormRequest
{
    private ?SchoolClass $class = null;

    /** @var list<Conflict> */
    private array $warnings = [];

    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        $calendar = $this->calendar();
        $event = $this->event();

        if ($event !== null && ! $user->can('changeEvent', [$calendar, $event])) {
            return false;
        }

        return $user->can('addEvent', [$calendar, $this->schoolClass()]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'kind' => ['required', Rule::enum(CalendarEventKind::class)],
            'exam_type' => $this->input('kind') === CalendarEventKind::Exam->value
                ? ['required', Rule::enum(ExamType::class)]
                : ['nullable', 'prohibited'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
            'class' => ['nullable', 'string'],
            'confirmed' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => __('calendar.fields.title'),
            'kind' => __('calendar.fields.kind'),
            'exam_type' => __('calendar.fields.exam_type'),
            'starts_on' => __('calendar.fields.starts_on'),
            'ends_on' => __('calendar.fields.ends_on'),
            'class' => __('calendar.fields.class'),
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

            if ($this->filled('class') && $this->schoolClass() === null) {
                $validator->errors()->add('class', __('calendar.errors.class_missing'));

                return;
            }

            $year = $this->academicYear();
            [$from, $to] = [$this->day('starts_on'), $this->day('ends_on')];

            if ($year === null || $from->lt($year->starts_on) || $to->gt($year->ends_on)) {
                $validator->errors()->add('starts_on', __('calendar.errors.outside_year', ['year' => $this->calendar()->academic_year]));

                return;
            }

            $conflicts = app(ConflictChecker::class)->forEvent(
                $this->calendar(),
                CalendarEventKind::from($this->string('kind')->toString()),
                $this->filled('exam_type') ? ExamType::from($this->string('exam_type')->toString()) : null,
                $from,
                $to,
                $this->schoolClass(),
            );

            foreach ($conflicts as $conflict) {
                if ($conflict->isError()) {
                    $validator->errors()->add('starts_on', $conflict->message);
                } else {
                    $this->warnings[] = $conflict;
                }
            }
        }];
    }

    /**
     * @return array{title: string, kind: string, exam_type: string|null, starts_on: string, ends_on: string, class_id: int|null}
     */
    public function eventData(): array
    {
        return [
            'title' => $this->string('title')->trim()->toString(),
            'kind' => $this->string('kind')->toString(),
            'exam_type' => $this->input('kind') === CalendarEventKind::Exam->value ? $this->string('exam_type')->toString() : null,
            'starts_on' => $this->string('starts_on')->toString(),
            'ends_on' => $this->string('ends_on')->toString(),
            'class_id' => $this->schoolClass()?->id,
        ];
    }

    /** @return list<Conflict> */
    public function warnings(): array
    {
        return $this->warnings;
    }

    public function schoolClass(): ?SchoolClass
    {
        if ($this->class === null && $this->filled('class')) {
            $year = $this->academicYear();
            $this->class = $year === null ? null : SchoolClass::query()->whereBelongsTo($year)->where('public_id', $this->string('class')->toString())->first();
        }

        return $this->class;
    }

    public function calendar(): SchoolCalendar
    {
        $calendar = $this->route('calendar');

        return $calendar instanceof SchoolCalendar ? $calendar : abort(404);
    }

    public function event(): ?CalendarEvent
    {
        $event = $this->route('event');

        return $event instanceof CalendarEvent ? $event : null;
    }

    private function academicYear(): ?AcademicYear
    {
        return AcademicYear::query()->where('name', $this->calendar()->academic_year)->first();
    }

    private function day(string $key): CarbonImmutable
    {
        return CarbonImmutable::parse($this->string($key)->toString());
    }
}
