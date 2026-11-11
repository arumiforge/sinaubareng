<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// A draft timetable for one class or every class of a year, made by a `default` queue
// job (D-262). Nothing reaches `timetable_slots` until the operator saves it.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_drafts', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->comment('NULL = every class of the year')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('seed')->comment('Acak ulang makes a new seed');
            $table->string('status', 16);
            $table->json('result')->nullable()->comment('Rows and unplaced JP per class');
            $table->timestamps();
            $table->index(['school_id', 'academic_year_id', 'created_at']);
        });

        DB::statement("ALTER TABLE timetable_drafts ADD CONSTRAINT timetable_drafts_status_check CHECK (status IN ('queued','running','ready','failed','saved'))");
    }
};
