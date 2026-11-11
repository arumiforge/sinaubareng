<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MembershipRole;
use App\Models\SchoolClass;
use App\Models\User;
use App\Tenancy\SchoolContext;

/**
 * Operator portal > Kelas: classes and who teaches what in them (D-032). Teachers see
 * their own classes later, under Kelas Saya.
 */
final class SchoolClassPolicy
{
    public function __construct(private readonly SchoolContext $context) {}

    public function viewAny(User $user): bool
    {
        return $this->isOperator();
    }

    public function view(User $user, SchoolClass $class): bool
    {
        return $this->isOperator() && $class->school_id === $this->context->schoolId();
    }

    public function create(User $user): bool
    {
        return $this->isOperator();
    }

    /** Also covers the class's teaching assignments. */
    public function update(User $user, SchoolClass $class): bool
    {
        return $this->view($user, $class);
    }

    public function delete(User $user, SchoolClass $class): bool
    {
        return $this->view($user, $class);
    }

    /** Susun draf jadwal (D-262): the operator only. */
    public function arrangeTimetable(User $user): bool
    {
        return $this->isOperator();
    }

    /** Kelas Saya > Jadwal: a teacher sees their own lessons and their homeroom classes' timetables. */
    public function viewOwnTimetable(User $user): bool
    {
        return $this->context->schoolId() !== null && $this->context->role() === MembershipRole::Teacher;
    }

    private function isOperator(): bool
    {
        return $this->context->schoolId() !== null && $this->context->role() === MembershipRole::Operator;
    }
}
