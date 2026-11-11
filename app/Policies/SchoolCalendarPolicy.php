<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MembershipRole;
use App\Models\CalendarEvent;
use App\Models\SchoolCalendar;
use App\Models\SchoolClass;
use App\Models\User;
use App\Tenancy\SchoolContext;

/**
 * Kalender (D-256, D-258): every member of the school reads it; the operator manages the
 * calendar and adds events for the school or any class; a wali kelas adds events for
 * their own class only; guru mapel only read.
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

    /** Create, copy from the region, apply region changes, cuti bersama school days. */
    public function manage(User $user, ?SchoolCalendar $calendar = null): bool
    {
        return $this->context->role() === MembershipRole::Operator
            && $this->context->schoolId() !== null
            && ($calendar === null || $calendar->school_id === $this->context->schoolId());
    }

    /** Adding an event for `$class`, or for the whole school when it is null. */
    public function addEvent(User $user, SchoolCalendar $calendar, ?SchoolClass $class): bool
    {
        if ($this->manage($user, $calendar)) {
            return true;
        }

        return $this->view($user, $calendar) && $this->isHomeroom($user, $class);
    }

    public function changeEvent(User $user, SchoolCalendar $calendar, CalendarEvent $event): bool
    {
        if ($event->calendar_id !== $calendar->id) {
            return false;
        }

        return $this->manage($user, $calendar) || ($this->view($user, $calendar) && $this->isHomeroom($user, $event->loadMissing('schoolClass')->schoolClass));
    }

    private function isHomeroom(User $user, ?SchoolClass $class): bool
    {
        return $class !== null
            && $this->context->role() === MembershipRole::Teacher
            && $class->homeroom_teacher_id === $user->id;
    }
}
