<?php

declare(strict_types=1);

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->string('status');
            $table->date('started_on');
            $table->date('ended_on')->nullable()->comment('NULL while the membership is active');
            // One active principal per school: the unique index ignores NULLs (data model).
            $table->unsignedBigInteger('active_principal_school_id')
                ->virtualAs("CASE WHEN role = 'principal' AND status = 'active' THEN school_id END")
                ->nullable()
                ->unique();
            $table->timestamps();
            $table->unique(['school_id', 'user_id', 'role']);
        });

        $roles = implode("','", array_column(MembershipRole::cases(), 'value'));
        $statuses = implode("','", array_column(MembershipStatus::cases(), 'value'));
        DB::statement("ALTER TABLE school_memberships ADD CONSTRAINT school_memberships_role_check CHECK (role IN ('{$roles}'))");
        DB::statement("ALTER TABLE school_memberships ADD CONSTRAINT school_memberships_status_check CHECK (status IN ('{$statuses}'))");
        DB::statement('ALTER TABLE school_memberships ADD CONSTRAINT school_memberships_dates_check CHECK (ended_on IS NULL OR ended_on >= started_on)');
    }
};
