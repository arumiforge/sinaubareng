<?php

declare(strict_types=1);

use App\Enums\CalendarChangeAction;
use App\Enums\CalendarChangeStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Calendar changes proposed by a wali kelas, guru mapel, or principal; the operator
// approves or rejects them, so each school keeps one calendar (D-380).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_change_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('calendar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 8);
            $table->foreignId('event_id')->nullable()->comment('The event to change or delete')->constrained('calendar_events')->nullOnDelete();
            $table->json('payload')->nullable()->comment('Proposed event fields for create and update');
            $table->string('reason', 255)->nullable();
            $table->string('status', 10);
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('decision_note', 255)->nullable();
            $table->timestamps();
            $table->index(['calendar_id', 'status']);
        });

        $actions = implode("','", array_column(CalendarChangeAction::cases(), 'value'));
        $statuses = implode("','", array_column(CalendarChangeStatus::cases(), 'value'));
        DB::statement("ALTER TABLE calendar_change_requests ADD CONSTRAINT calendar_change_requests_action_check CHECK (action IN ('{$actions}'))");
        DB::statement("ALTER TABLE calendar_change_requests ADD CONSTRAINT calendar_change_requests_status_check CHECK (status IN ('{$statuses}'))");
        // `event_id` has an FK action, so MySQL forbids it in a CHECK (error 3823); the payload rule is enough here.
        DB::statement("ALTER TABLE calendar_change_requests ADD CONSTRAINT calendar_change_requests_payload_check CHECK (action = 'delete' OR payload IS NOT NULL)");
    }
};
