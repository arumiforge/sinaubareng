<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// A school's own switch for local and optional content (D-204).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('grade')->nullable()->comment('NULL = every grade');
            // One row per school, subject, and grade, with NULL (every grade) counted as one value.
            $table->unsignedTinyInteger('grade_key')->storedAs('coalesce(`grade`, 0)');
            $table->boolean('enabled');
            $table->boolean('in_report');
            $table->timestamps();
            $table->unique(['school_id', 'subject_id', 'grade_key'], 'school_subjects_natural_unique');
            $table->index('subject_id');
        });

        DB::statement('ALTER TABLE school_subjects ADD CONSTRAINT school_subjects_grade_check CHECK (grade IS NULL OR grade BETWEEN 1 AND 6)');
    }
};
