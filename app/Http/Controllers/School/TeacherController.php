<?php

declare(strict_types=1);

namespace App\Http\Controllers\School;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\SchoolMembership;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Operator portal > Guru: the school's teachers and the invitations still open.
 */
final class TeacherController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Invitation::class);

        $teachers = SchoolMembership::query()
            ->where('role', MembershipRole::Teacher)
            ->with('user:id,name,email')
            ->get()
            ->sortBy(fn (SchoolMembership $membership): string => $membership->user->name)
            ->map(fn (SchoolMembership $membership): array => [
                'id' => $membership->public_id,
                'name' => $membership->user->name,
                'email' => $membership->user->email,
                'status' => $membership->status->label(),
                'is_active' => $membership->status === MembershipStatus::Active,
            ])
            ->values();

        $invitations = Invitation::query()->open()
            ->where('role', MembershipRole::Teacher)
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Invitation $invitation): array => [
                'id' => $invitation->public_id,
                'email' => $invitation->email,
                'status' => $invitation->status()->value,
                'status_label' => $invitation->status()->label(),
                'expires_at' => $invitation->expires_at->timezone('Asia/Jakarta')->format('d/m/Y'),
            ]);

        return Inertia::render('School/Teachers', [
            'teachers' => $teachers,
            'invitations' => $invitations,
        ]);
    }
}
