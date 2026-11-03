<?php

declare(strict_types=1);

namespace App\Tenancy;

use App\Enums\MembershipRole;

/**
 * The school (and role) the current request or job acts for. Registered as a scoped
 * singleton, so it starts empty for every request and every queued job.
 */
final class SchoolContext
{
    private ?int $schoolId = null;

    private ?MembershipRole $role = null;

    public function set(int $schoolId, ?MembershipRole $role = null): void
    {
        $this->schoolId = $schoolId;
        $this->role = $role;
    }

    public function clear(): void
    {
        $this->schoolId = null;
        $this->role = null;
    }

    public function schoolId(): ?int
    {
        return $this->schoolId;
    }

    public function role(): ?MembershipRole
    {
        return $this->role;
    }

    /**
     * Runs the callback for the given school and restores the previous context (TEN-3).
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    public function runFor(int $schoolId, callable $callback): mixed
    {
        [$previousSchoolId, $previousRole] = [$this->schoolId, $this->role];
        $this->set($schoolId);

        try {
            return $callback();
        } finally {
            $this->schoolId = $previousSchoolId;
            $this->role = $previousRole;
        }
    }
}
