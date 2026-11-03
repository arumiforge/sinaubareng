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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('npsn', 8)->nullable()->unique()->comment('NULL until verified');
            $table->string('level');
            $table->foreignId('region_id')->constrained()->restrictOnDelete();
            $table->text('address')->nullable()->comment('NULL until the school fills it in');
            $table->timestamps();
        });

        $levels = implode("','", array_column(SchoolLevel::cases(), 'value'));
        DB::statement("ALTER TABLE schools ADD CONSTRAINT schools_level_check CHECK (level IN ('{$levels}'))");
    }
};
