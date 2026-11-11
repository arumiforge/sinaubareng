<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A school's day frame and soft preference switches for "Susun draf jadwal" (D-262).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('day_frame')->comment('{days: {weekday: {starts_at, periods}}, breaks: [{after_period, minutes, label}]}');
            $table->json('preferences')->comment('On/off switches, no weights (D-262)');
            $table->timestamps();
        });
    }
};
