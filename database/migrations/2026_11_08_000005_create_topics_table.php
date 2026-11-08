<?php

declare(strict_types=1);

use App\Enums\Phase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Materi pokok / ATP: one row per topic of a grade, subject, and semester.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_version_id')->constrained()->cascadeOnDelete();
            $table->string('code', 96)->unique();
            $table->json('legacy_codes')->comment('EDUNEXA Legacy_ID aliases; [] when there are none');
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('grade');
            $table->char('phase', 1);
            $table->unsignedTinyInteger('semester');
            $table->text('elements');
            $table->string('title');
            $table->text('cp_text');
            $table->string('atp_code', 64);
            $table->unsignedSmallInteger('order_in_semester');
            $table->unsignedSmallInteger('jp_total');
            $table->unsignedSmallInteger('jp_per_week');
            $table->unsignedTinyInteger('week_start');
            $table->unsignedTinyInteger('week_end');
            $table->json('weekly_distribution');
            $table->json('references');
            $table->json('audit')->nullable()->comment('EDUNEXA Audit_Data; NULL when the source has none');
            $table->json('extras')->nullable()->comment('sub_element, art branch, science process skills; NULL when the source has none');
            $table->timestamps();
            $table->index(['curriculum_version_id', 'subject_id', 'grade', 'semester'], 'topics_lookup_index');
        });

        $phases = implode("','", array_column(Phase::cases(), 'value'));
        DB::statement('ALTER TABLE topics ADD CONSTRAINT topics_grade_check CHECK (grade BETWEEN 1 AND 6)');
        DB::statement("ALTER TABLE topics ADD CONSTRAINT topics_phase_check CHECK (phase IN ('{$phases}'))");
        DB::statement('ALTER TABLE topics ADD CONSTRAINT topics_semester_check CHECK (semester IN (1, 2))');
        DB::statement('ALTER TABLE topics ADD CONSTRAINT topics_weeks_check CHECK (week_start >= 1 AND week_start <= week_end)');
    }
};
