<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Default local content per region, e.g. Bahasa Jawa for Kudus (D-018).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('region_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('allocation')->comment('JP per week');
            $table->timestamps();
            $table->unique(['region_id', 'subject_id']);
            $table->index('subject_id');
        });

        DB::statement('ALTER TABLE region_subjects ADD CONSTRAINT region_subjects_allocation_check CHECK (allocation BETWEEN 1 AND 6)');
    }
};
