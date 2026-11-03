<?php

declare(strict_types=1);

use App\Enums\SchoolLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reference list of SD/MI from the Kemendikdasmen reference site (D-169). Not tenants.
        Schema::create('school_directory_entries', function (Blueprint $table) {
            $table->id();
            $table->string('npsn', 8)->unique();
            $table->string('name');
            $table->string('level');
            $table->string('address');
            $table->string('village');
            $table->string('district');
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->date('fetched_on');
            $table->timestamps();
        });

        $levels = implode("','", array_column(SchoolLevel::cases(), 'value'));
        DB::statement("ALTER TABLE school_directory_entries ADD CONSTRAINT school_directory_entries_level_check CHECK (level IN ('{$levels}'))");
    }
};
