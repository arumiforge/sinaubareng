<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// A class (rombel) of one grade in one academic year (data model, School year).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('grade');
            $table->string('name', 20)->comment('e.g. 4A');
            $table->foreignId('homeroom_teacher_id')->nullable()->comment('Wali kelas; NULL = not assigned yet')
                ->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['academic_year_id', 'name']);
            $table->index(['school_id', 'academic_year_id', 'grade']);
        });

        DB::statement('ALTER TABLE classes ADD CONSTRAINT classes_grade_check CHECK (grade BETWEEN 1 AND 6)');
    }
};
