<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Actions\SchoolYear\FindCurrentAcademicYear;
use App\Calendar\Conflict;
use App\Calendar\EffectiveDays;
use App\Calendar\Impact\CalendarImpact;
use App\Calendar\Impact\MeetingImpact;
use App\Calendar\NationalHolidays;
use App\Calendar\RegionCalendarSync;
use App\Calendar\RegionChange;
use App\Enums\CalendarEventKind;
use App\Enums\ExamType;
use App\Enums\NationalHolidayKind;
use App\Http\Controllers\Controller;
use App\Http\Requests\School\SaveCalendarEventRequest;
use App\Models\AcademicYear;
use App\Models\CalendarEvent;
use App\Models\School;
use App\Models\SchoolCalendar;
use App\Models\SchoolClass;
use App\Models\User;
use App\Tenancy\SchoolContext;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Kalender (D-253…D-261): national holidays merged with the school calendar, copied
 * from the region calendar with offered updates, school and class events, and the
 * effective days of a class. The calendar binds through the school scope, and an event
 * only through its calendar, so another school's calendar or event is a 404.
 */
final class CalendarController extends Controller
{
    public function show(Request $request, FindCurrentAcademicYear $findCurrent, NationalHolidays $holidays, EffectiveDays $effectiveDays, RegionCalendarSync $sync, SchoolContext $context): Response
    {
        Gate::authorize('viewAny', SchoolCalendar::class);

        /** @var User $user */
        $user = $request->user();
        $years = AcademicYear::query()->orderByDesc('starts_on')->get();
        $year = $request->filled('tahun')
            ? $years->firstWhere('public_id', $request->string('tahun')->toString()) ?? abort(404)
            : $findCurrent->handle();

        if ($year === null) {
            return Inertia::render('School/Calendar/Show', ['year' => null, 'years' => []]);
        }

        $calendar = SchoolCalendar::query()->where('academic_year', $year->name)->with('sourceCalendar.region')->first();
        $isOperator = Gate::allows('manage', SchoolCalendar::class);
        $classes = SchoolClass::query()->whereBelongsTo($year)->orderBy('grade')->orderBy('name')->get();
        $editableClassIds = $isOperator ? $classes->modelKeys() : $classes->where('homeroom_teacher_id', $user->id)->modelKeys();
        $school = School::query()->findOrFail($context->schoolId());

        $selected = $request->filled('kelas')
            ? $classes->firstWhere('public_id', $request->string('kelas')->toString())
            : ($classes->firstWhere('homeroom_teacher_id', $user->id) ?? $classes->first());

        $schoolDays = $calendar->joint_leave_school_days ?? [];
        $events = $calendar?->events()->with('schoolClass:id,public_id,name')->orderBy('starts_on')->get() ?? collect();
        $region = $calendar === null && $isOperator ? $sync->regionCalendarFor($school, $year) : null;
        $changes = $calendar !== null && $isOperator && $calendar->hasRegionUpdate() ? $sync->changes($calendar) : [];

        return Inertia::render('School/Calendar/Show', [
            'year' => ['id' => $year->public_id, 'name' => $year->name, 'starts_on' => $year->starts_on->toDateString(), 'ends_on' => $year->ends_on->toDateString()],
            'years' => $years->map(fn (AcademicYear $item): array => ['value' => $item->public_id, 'label' => $item->name])->values()->all(),
            'calendar' => $calendar === null ? null : [
                'id' => $calendar->public_id,
                'source' => $calendar->sourceCalendar?->name,
                'region_changes' => array_map(fn (RegionChange $change): array => $change->toArray(), $changes),
            ],
            'region_calendar' => $region?->name,
            'can_manage' => $isOperator,
            'events' => $events->map(fn (CalendarEvent $event): array => [
                'id' => $event->public_id,
                'title' => $event->title,
                'kind' => $event->kind->value,
                'kind_label' => $event->kind->label(),
                'exam_type' => $event->exam_type?->value,
                'exam_type_label' => $event->exam_type?->label(),
                'starts_on' => $event->starts_on->toDateString(),
                'ends_on' => $event->ends_on->toDateString(),
                'class' => $event->schoolClass?->public_id,
                'class_name' => $event->schoolClass?->name,
                'can_edit' => $isOperator || ($event->class_id !== null && in_array($event->class_id, $editableClassIds, true)),
            ])->values()->all(),
            'holidays' => array_values(array_map(fn ($holiday): array => [
                ...$holiday->toArray(),
                'is_school_day' => in_array($holiday->date, $schoolDays, true),
            ], $holidays->between($year->starts_on, $year->ends_on))),
            'classes' => $classes->map(fn (SchoolClass $class): array => [
                'value' => $class->public_id,
                'label' => $class->name,
                'editable' => in_array($class->id, $editableClassIds, true),
            ])->values()->all(),
            'selected_class' => $selected?->public_id,
            'effective_days' => $selected === null ? null : $effectiveDays->forClass($selected)->toArray(),
            'kinds' => collect(CalendarEventKind::cases())->map(fn (CalendarEventKind $kind): array => ['value' => $kind->value, 'label' => $kind->label()])->all(),
            'exam_types' => collect(ExamType::cases())->map(fn (ExamType $type): array => ['value' => $type->value, 'label' => $type->label()])->all(),
        ]);
    }

    /** "Salin dari kalender wilayah", or an empty calendar when the region has none yet. */
    public function store(Request $request, RegionCalendarSync $sync, SchoolContext $context): RedirectResponse
    {
        Gate::authorize('manage', SchoolCalendar::class);

        $year = AcademicYear::query()->where('public_id', $request->string('tahun')->toString())->firstOrFail();

        if (SchoolCalendar::query()->where('academic_year', $year->name)->exists()) {
            return $this->back($year)->with('toast', __('calendar.toasts.exists'));
        }

        $region = $sync->regionCalendarFor(School::query()->findOrFail($context->schoolId()), $year);

        if ($region !== null && $request->boolean('copy', true)) {
            $sync->copy($region, $year);

            return $this->back($year)->with('toast', __('calendar.toasts.copied', ['name' => $region->name]));
        }

        $sync->createEmpty($year);

        return $this->back($year)->with('toast', __('calendar.toasts.created'));
    }

    /** "Terapkan perubahan wilayah": apply or skip the region's changes (D-256, D-261). */
    public function regionChanges(Request $request, SchoolCalendar $calendar, RegionCalendarSync $sync, MeetingImpact $impact): RedirectResponse
    {
        Gate::authorize('manage', $calendar);

        $calendar->load('sourceCalendar');
        $year = $this->year($calendar);

        if ($request->input('action') === 'skip') {
            $sync->skip($calendar);

            return $this->back($year)->with('toast', __('calendar.toasts.region_skipped'));
        }

        $dates = array_merge([], ...array_map(fn (RegionChange $change): array => $change->dates(), $sync->changes($calendar)));
        $this->confirmImpact($request, $impact->forDates($calendar, $dates, null));
        $sync->apply($calendar);

        return $this->back($year)->with('toast', __('calendar.toasts.region_applied'));
    }

    /** Keep a cuti bersama date as a school day, or make it a holiday again (D-255). */
    public function jointLeave(Request $request, SchoolCalendar $calendar, NationalHolidays $holidays, MeetingImpact $impact): RedirectResponse
    {
        Gate::authorize('manage', $calendar);

        $request->validate(['date' => ['required', 'date_format:Y-m-d'], 'school_day' => ['required', 'boolean']]);
        $date = $request->string('date')->toString();
        $day = $holidays->between(CarbonImmutable::parse($date), CarbonImmutable::parse($date))[$date] ?? null;

        if ($day === null || $day->kind !== NationalHolidayKind::JointLeave) {
            throw ValidationException::withMessages(['date' => __('calendar.errors.not_joint_leave')]);
        }

        $this->confirmImpact($request, $impact->forDates($calendar, [$date], null));

        $days = array_values(array_diff($calendar->joint_leave_school_days ?? [], [$date]));

        if ($request->boolean('school_day')) {
            $days[] = $date;
            sort($days);
        }

        $calendar->update(['joint_leave_school_days' => $days]);

        return $this->back($this->year($calendar))->with('toast', __('calendar.toasts.saved'));
    }

    public function storeEvent(SaveCalendarEventRequest $request, SchoolCalendar $calendar, MeetingImpact $impact): RedirectResponse
    {
        $data = $request->eventData();
        $this->confirmImpact($request, $impact->forDates($calendar, self::dates($data['starts_on'], $data['ends_on']), $data['class_id']));

        $calendar->events()->create($data);

        return $this->saved($calendar, $request->warnings());
    }

    public function updateEvent(SaveCalendarEventRequest $request, SchoolCalendar $calendar, CalendarEvent $event, MeetingImpact $impact): RedirectResponse
    {
        $data = $request->eventData();
        $dates = [...self::dates($event->starts_on->toDateString(), $event->ends_on->toDateString()), ...self::dates($data['starts_on'], $data['ends_on'])];
        $this->confirmImpact($request, $impact->forDates($calendar, array_values(array_unique($dates)), $data['class_id'] ?? $event->class_id));

        $event->update($data);

        return $this->saved($calendar, $request->warnings());
    }

    public function destroyEvent(Request $request, SchoolCalendar $calendar, CalendarEvent $event, MeetingImpact $impact): RedirectResponse
    {
        Gate::authorize('changeEvent', [$calendar, $event]);

        $this->confirmImpact($request, $impact->forDates($calendar, self::dates($event->starts_on->toDateString(), $event->ends_on->toDateString()), $event->class_id));
        $event->delete();

        return $this->back($this->year($calendar))->with('toast', __('calendar.toasts.deleted'));
    }

    /**
     * D-261: a change that moves RPM meetings is confirmed first. The page shows the
     * message and sends the request again with `confirmed`.
     */
    private function confirmImpact(Request $request, CalendarImpact $impact): void
    {
        if (! $impact->isEmpty() && ! $request->boolean('confirmed')) {
            throw ValidationException::withMessages(['confirm_impact' => $impact->message()]);
        }
    }

    /**
     * @param  list<Conflict>  $warnings
     */
    private function saved(SchoolCalendar $calendar, array $warnings): RedirectResponse
    {
        $toast = __('calendar.toasts.saved');

        if ($warnings !== []) {
            $toast .= ' '.__('calendar.toasts.warning', ['warnings' => implode(' ', array_map(fn (Conflict $warning): string => $warning->message, $warnings))]);
        }

        return $this->back($this->year($calendar))->with('toast', $toast);
    }

    /**
     * @return list<string>
     */
    private static function dates(string $from, string $to): array
    {
        $dates = [];

        for ($day = CarbonImmutable::parse($from); $day->lte(CarbonImmutable::parse($to)); $day = $day->addDay()) {
            $dates[] = $day->toDateString();
        }

        return $dates;
    }

    private function year(SchoolCalendar $calendar): ?AcademicYear
    {
        return AcademicYear::query()->where('name', $calendar->academic_year)->first();
    }

    private function back(?AcademicYear $year): RedirectResponse
    {
        return to_route('calendar.show', $year === null ? [] : ['tahun' => $year->public_id]);
    }
}
