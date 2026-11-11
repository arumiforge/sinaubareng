<?php

declare(strict_types=1);

use App\Enums\NationalHolidayKind;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// One central, read-only list of national holidays and cuti bersama (D-253). It sits
// outside `calendars` and is merged into every calendar view.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('national_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name');
            $table->string('kind', 20);
            $table->string('decree')->nullable()->comment('Short SKB reference');
            $table->string('academic_year', 9)->comment('e.g. 2026/2027');
            $table->boolean('is_provisional')->default(false)->comment('"sementara": not yet in an SKB (D-253)');
            $table->timestamps();
            $table->index('academic_year');
        });

        $kinds = implode("','", array_column(NationalHolidayKind::cases(), 'value'));
        DB::statement("ALTER TABLE national_holidays ADD CONSTRAINT national_holidays_kind_check CHECK (kind IN ('{$kinds}'))");
    }
};
