<?php

declare(strict_types=1);

use App\Enums\CalendarStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Region reference calendars and school calendars (data model, Calendar; D-256, D-323).
// A school copies a region calendar and later sees the region's changes as an offer.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('region_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('school_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('academic_year', 9)->comment('e.g. 2026/2027');
            $table->string('code', 64)->nullable()->unique()->comment('Region calendars, e.g. KALDIK-JATENG-2026-2027-6H');
            $table->string('name')->nullable();
            $table->string('status', 16)->comment('Region calendars stay draft until Tim Validasi signs off (D-113)');
            $table->unsignedTinyInteger('school_days_per_week')->nullable()->comment('Region default, 5 or 6');
            $table->date('starts_on')->nullable()->comment('First school day by the region calendar');
            $table->date('even_semester_starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->unsignedInteger('version')->default(1)->comment('Region calendars: +1 on every change (D-256)');
            $table->foreignId('source_calendar_id')->nullable()->comment('School copy: the region calendar it came from')->constrained('calendars')->nullOnDelete();
            $table->unsignedInteger('source_version')->nullable()->comment('Region version last applied or skipped');
            $table->json('joint_leave_school_days')->nullable()->comment('Cuti bersama dates kept as school days (D-255)');
            $table->json('meta')->nullable()->comment('Region calendars: source file, sha256, expected effective days');
            // MySQL refuses a CHECK on a column whose FK cascades (error 3823), so the owner rule checks this instead.
            $table->unsignedTinyInteger('owner_nulls')->virtualAs('(region_id IS NULL) + (school_id IS NULL)')->comment('1 = exactly one owner');
            $table->timestamps();
            $table->unique(['region_id', 'academic_year']);
            $table->unique(['school_id', 'academic_year']);
        });

        $statuses = implode("','", array_column(CalendarStatus::cases(), 'value'));
        DB::statement("ALTER TABLE calendars ADD CONSTRAINT calendars_status_check CHECK (status IN ('{$statuses}'))");
        DB::statement('ALTER TABLE calendars ADD CONSTRAINT calendars_owner_check CHECK (owner_nulls = 1)');
        DB::statement('ALTER TABLE calendars ADD CONSTRAINT calendars_days_check CHECK (school_days_per_week IS NULL OR school_days_per_week BETWEEN 5 AND 6)');
    }
};
