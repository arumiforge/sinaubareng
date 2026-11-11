<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MembershipRole;
use App\Models\CalendarEvent;
use App\Models\SchoolCalendar;
use App\Models\User;
use App\Tenancy\SchoolContext;

/**
 * Kalender: one calendar per school, kept by the operator (D-380). Every member reads it.
 * Wali kelas, guru mapel, and the principal propose changes, which the operator
 * approves or rejects (replaces the direct wali kelas edits of D-258).
 */
final class SchoolCalendarPolicy
{
    public function __construct(private readonly SchoolContext $context) {}

    public function viewAny(User $user): bool
    {
        return $this->context->schoolId() !== null && $this->context->role() !== null;
    }

    public function view(User $user, SchoolCalendar $calendar): bool
    {
        return $this->viewAny($user) && $calendar->school_id === $this->context->schoolId();
    }

    /** Create, copy from the region, apply region changes, edit events, decide on proposals. */
    public function manage(User $user, ?SchoolCalendar $calendar = null): bool
    {
        return $this->context->role() === MembershipRole::Operator
            && $this->context->schoolId() !== null
            && ($calendar === null || $calendar->school_id === $this->context->schoolId());
    }

    /** Wali kelas, guru mapel, and the principal send changes to the operator (D-380). */
    public function propose(User $user, SchoolCalendar $calendar): bool
    {
        return $this->view($user, $calendar)
            && in_array($this->context->role(), [MembershipRole::Teacher, MembershipRole::Principal], true);
    }

    /** Changing or deleting an event of this calendar, directly or as a proposal. */
    public function touchEvent(User $user, SchoolCalendar $calendar, CalendarEvent $event): bool
    {
        return $event->calendar_id === $calendar->id && ($this->manage($user, $calendar) || $this->propose($user, $calendar));
    }
}
