<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// A school's academic year with its two semesters (data model, School year).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name', 9)->comment('e.g. 2026/2027, from the year starts_on falls in');
            $table->date('starts_on');
            $table->date('even_semester_starts_on')->comment('First day of semester 2 (genap)');
            $table->date('ends_on');
            $table->timestamps();
            $table->unique(['school_id', 'name']);
            $table->index(['school_id', 'starts_on']);
        });

        DB::statement('ALTER TABLE academic_years ADD CONSTRAINT academic_years_dates_check CHECK (starts_on < even_semester_starts_on AND even_semester_starts_on < ends_on)');
    }
};
