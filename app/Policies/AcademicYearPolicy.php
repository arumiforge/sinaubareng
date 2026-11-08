<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MembershipRole;
use App\Models\AcademicYear;
use App\Models\User;
use App\Tenancy\SchoolContext;

/**
 * Operator portal > Tahun Ajaran (product concept, Operator flow). Only the school's
 * operator sets up its academic years.
 */
final class AcademicYearPolicy
{
    public function __construct(private readonly SchoolContext $context) {}

    public function viewAny(User $user): bool
    {
        return $this->isOperator();
    }

    public function create(User $user): bool
    {
        return $this->isOperator();
    }

    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $this->isOperator() && $academicYear->school_id === $this->context->schoolId();
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $this->update($user, $academicYear);
    }

    /** Copy the previous year's classes into this year (D-242). */
    public function copyClasses(User $user, AcademicYear $academicYear): bool
    {
        return $this->update($user, $academicYear);
    }

    private function isOperator(): bool
    {
        return $this->context->schoolId() !== null && $this->context->role() === MembershipRole::Operator;
    }
}
