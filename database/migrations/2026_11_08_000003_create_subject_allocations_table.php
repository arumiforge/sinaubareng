<?php

declare(strict_types=1);

use App\Enums\LearningType;
use App\Enums\Phase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('grade');
            $table->char('phase', 1);
            $table->string('learning_type');
            $table->unsignedSmallInteger('jp_per_week');
            $table->unsignedTinyInteger('weeks_per_year');
            $table->unsignedSmallInteger('s1_jp');
            $table->unsignedTinyInteger('s1_effective_weeks');
            $table->unsignedSmallInteger('s2_jp');
            $table->unsignedTinyInteger('s2_effective_weeks');
            $table->timestamps();
            $table->unique(['curriculum_version_id', 'subject_id', 'grade', 'learning_type'], 'subject_allocations_natural_unique');
            $table->index(['subject_id', 'grade']);
        });

        $phases = implode("','", array_column(Phase::cases(), 'value'));
        $types = implode("','", array_column(LearningType::cases(), 'value'));
        DB::statement('ALTER TABLE subject_allocations ADD CONSTRAINT subject_allocations_grade_check CHECK (grade BETWEEN 1 AND 6)');
        DB::statement("ALTER TABLE subject_allocations ADD CONSTRAINT subject_allocations_phase_check CHECK (phase IN ('{$phases}'))");
        DB::statement("ALTER TABLE subject_allocations ADD CONSTRAINT subject_allocations_learning_type_check CHECK (learning_type IN ('{$types}'))");
        DB::statement('ALTER TABLE subject_allocations ADD CONSTRAINT subject_allocations_weeks_check CHECK (s1_effective_weeks + s2_effective_weeks = weeks_per_year)');
    }
};
