<?php

declare(strict_types=1);

namespace App\Calendar;

use App\Enums\CalendarChangeAction;
use App\Enums\CalendarChangeStatus;
use App\Enums\CalendarEventKind;
use App\Enums\ExamType;
use App\Models\AcademicYear;
use App\Models\CalendarChangeRequest;
use App\Models\SchoolCalendar;
use App\Models\SchoolClass;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Proposals for the school calendar (D-380): a wali kelas, guru mapel, or principal
 * proposes; the operator approves, and the change is checked again with the same rules
 * as a direct edit (ConflictChecker, the school year) before it is applied.
 */
final readonly class CalendarChanges
{
    public function __construct(private ConflictChecker $conflicts) {}

    /**
     * @param  array{title: string, kind: string, exam_type: string|null, starts_on: string, ends_on: string, class_id: int|null}|null  $payload
     */
    public function propose(SchoolCalendar $calendar, User $user, CalendarChangeAction $action, ?array $payload, ?int $eventId, ?string $reason): CalendarChangeRequest
    {
        return $calendar->changeRequests()->create([
            'school_id' => $calendar->school_id,
            'requested_by' => $user->id,
            'action' => $action,
            'event_id' => $eventId,
            'payload' => $payload,
            'reason' => $reason === '' ? null : $reason,
            'status' => CalendarChangeStatus::Pending,
        ]);
    }

    /**
     * The dates whose status the change touches, for the impact preview (D-261).
     *
     * @return list<string>
     */
    public function dates(CalendarChangeRequest $change): array
    {
        $ranges = [];

        if ($change->payload !== null) {
            $ranges[] = [$change->payload['starts_on'], $change->payload['ends_on']];
        }

        if ($change->event !== null) {
            $ranges[] = [$change->event->starts_on->toDateString(), $change->event->ends_on->toDateString()];
        }

        $dates = [];

        foreach ($ranges as [$from, $to]) {
            for ($day = CarbonImmutable::parse($from); $day->lte(CarbonImmutable::parse($to)); $day = $day->addDay()) {
                $dates[] = $day->toDateString();
            }
        }

        return array_values(array_unique($dates));
    }

    /**
     * @return list<Conflict> warnings; errors throw
     *
     * @throws ValidationException
     */
    public function approve(CalendarChangeRequest $change, SchoolCalendar $calendar, User $operator): array
    {
        $change->loadMissing('event');
        $this->ensurePending($change);
        $warnings = [];

        if ($change->action !== CalendarChangeAction::Create && $change->event === null) {
            throw ValidationException::withMessages(['change' => __('calendar.proposals.errors.event_gone')]);
        }

        if ($change->payload !== null) {
            $warnings = $this->check($calendar, $change->payload);
        }

        DB::transaction(function () use ($change, $calendar, $operator): void {
            if ($change->action === CalendarChangeAction::Create) {
                $calendar->events()->create((array) $change->payload);
            } elseif ($change->action === CalendarChangeAction::Update) {
                $change->event?->update((array) $change->payload);
            } else {
                $change->event?->delete();
            }

            $change->update(['status' => CalendarChangeStatus::Approved, 'decided_by' => $operator->id, 'decided_at' => now()]);
        });

        return $warnings;
    }

    public function reject(CalendarChangeRequest $change, User $operator, ?string $note): void
    {
        $this->ensurePending($change);

        $change->update([
            'status' => CalendarChangeStatus::Rejected,
            'decided_by' => $operator->id,
            'decided_at' => now(),
            'decision_note' => $note === '' ? null : $note,
        ]);
    }

    private function ensurePending(CalendarChangeRequest $change): void
    {
        if ($change->status !== CalendarChangeStatus::Pending) {
            throw ValidationException::withMessages(['change' => __('calendar.proposals.errors.decided')]);
        }
    }

    /**
     * The direct-edit rules, again at approval time: the year, the class, and conflicts.
     *
     * @param  array{title: string, kind: string, exam_type: string|null, starts_on: string, ends_on: string, class_id: int|null}  $payload
     * @return list<Conflict>
     */
    private function check(SchoolCalendar $calendar, array $payload): array
    {
        $year = AcademicYear::query()->where('name', $calendar->academic_year)->first();
        [$from, $to] = [CarbonImmutable::parse($payload['starts_on']), CarbonImmutable::parse($payload['ends_on'])];

        if ($year === null || $from->lt($year->starts_on) || $to->gt($year->ends_on)) {
            throw ValidationException::withMessages(['change' => __('calendar.errors.outside_year', ['year' => $calendar->academic_year])]);
        }

        $class = $payload['class_id'] === null ? null : SchoolClass::query()->whereBelongsTo($year)->find($payload['class_id']);

        if ($payload['class_id'] !== null && $class === null) {
            throw ValidationException::withMessages(['change' => __('calendar.errors.class_missing')]);
        }

        $conflicts = $this->conflicts->forEvent(
            $calendar,
            CalendarEventKind::from($payload['kind']),
            $payload['exam_type'] === null ? null : ExamType::from($payload['exam_type']),
            $from,
            $to,
            $class,
        );

        foreach ($conflicts as $conflict) {
            if ($conflict->isError()) {
                throw ValidationException::withMessages(['change' => $conflict->message]);
            }
        }

        return $conflicts;
    }
}
