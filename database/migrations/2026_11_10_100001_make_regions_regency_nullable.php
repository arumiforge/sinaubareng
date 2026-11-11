<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A province-level region has no regency: it owns the province's reference calendar,
// which every regency of the province uses until it gets its own (D-323).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropUnique(['province', 'regency']);
        });

        Schema::table('regions', function (Blueprint $table) {
            $table->string('regency')->nullable()->comment('NULL for a province-level region (D-323)')->change();
            $table->string('regency_key')->virtualAs("coalesce(regency, '')")->comment('Makes the province-level row unique too');
            $table->unique(['province', 'regency_key']);
        });
    }
};
