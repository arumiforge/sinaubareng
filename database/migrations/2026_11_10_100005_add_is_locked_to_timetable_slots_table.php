<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A locked row stays as it is when a draft timetable is made or saved (D-262).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetable_slots', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false)->after('jp')->comment('Kept by the draft generator (D-262)');
        });
    }
};
