<?php

declare(strict_types=1);

use App\Enums\TimetableSlotKind;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// One row of a class's weekly timetable (data model, Calendar/timetable; D-247…D-250).
// The teacher is not stored: it comes from the class's teaching assignment (D-249).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday')->comment('1 = Senin … 6 = Sabtu');
            $table->unsignedTinyInteger('period_no')->comment('Place in the day by start time, recomputed (D-247)');
            $table->time('starts_at');
            $table->time('ends_at');
            $table->string('kind', 16);
            $table->foreignId('subject_id')->nullable()->comment('Lessons only (D-247)')->constrained()->restrictOnDelete();
            $table->string('label', 40)->nullable()->comment('Optional name of a non-lesson row, e.g. Senam');
            $table->unsignedTinyInteger('jp')->comment('Lessons 1–4, other rows 0 (D-250)');
            $table->timestamps();
            $table->unique(['class_id', 'weekday', 'period_no']);
            $table->index(['school_id', 'weekday', 'starts_at']);
            $table->index('subject_id');
        });

        $kinds = implode("','", array_column(TimetableSlotKind::cases(), 'value'));
        DB::statement("ALTER TABLE timetable_slots ADD CONSTRAINT timetable_slots_kind_check CHECK (kind IN ('{$kinds}'))");
        DB::statement('ALTER TABLE timetable_slots ADD CONSTRAINT timetable_slots_weekday_check CHECK (weekday BETWEEN 1 AND 6)');
        DB::statement('ALTER TABLE timetable_slots ADD CONSTRAINT timetable_slots_time_check CHECK (starts_at < ends_at)');
        DB::statement("ALTER TABLE timetable_slots ADD CONSTRAINT timetable_slots_lesson_check CHECK (
            (kind = 'lesson' AND subject_id IS NOT NULL AND jp BETWEEN 1 AND 4)
            OR (kind <> 'lesson' AND subject_id IS NULL AND jp = 0)
        )");
    }
};
