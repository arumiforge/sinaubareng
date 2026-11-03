<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervisor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('started_on');
            $table->date('ended_on')->nullable()->comment('NULL while the assignment is current');
            $table->timestamps();
            $table->unique(['school_id', 'user_id', 'started_on']);
        });

        DB::statement('ALTER TABLE supervisor_assignments ADD CONSTRAINT supervisor_assignments_dates_check CHECK (ended_on IS NULL OR ended_on >= started_on)');
    }
};
