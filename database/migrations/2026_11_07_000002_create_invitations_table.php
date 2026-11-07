<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('role');
            $table->char('token_hash', 64)->unique()->comment('SHA-256 of the token in the mailed link; the token itself is never stored');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable()->comment('NULL while the invitation is open');
            $table->foreignId('invited_by')->nullable()->comment('NULL when Tim SIGRAK invited from the staff console, or the inviter was deleted')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('invited_by_staff_id')->nullable()->comment('Set when Tim SIGRAK invited from the staff console (D-182)')
                ->constrained('staff_users')->nullOnDelete();
            $table->timestamps();
            $table->index(['school_id', 'email']);
        });

        $roles = implode("','", array_column(MembershipRole::cases(), 'value'));
        DB::statement("ALTER TABLE invitations ADD CONSTRAINT invitations_role_check CHECK (role IN ('{$roles}'))");
    }
};
