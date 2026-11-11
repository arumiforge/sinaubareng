<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Minimal versioning before P5-04 (D-355): a curriculum version is the curriculum kind
// of rule set (D-354). Columns are added, the table is not rebuilt.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curriculum_versions', function (Blueprint $table) {
            $table->date('effective_on')->nullable()->after('status')->comment('When the regulation behind it is in force');
            $table->string('applies_from_school_year', 9)->nullable()->after('effective_on')->comment('e.g. 2026/2027; NULL = every year (D-354)');
            $table->foreignId('supersedes_id')->nullable()->after('applies_from_school_year')->comment('The version this one replaces')
                ->constrained('curriculum_versions')->nullOnDelete();
        });

        DB::statement("ALTER TABLE curriculum_versions ADD CONSTRAINT curriculum_versions_school_year_check CHECK (
            applies_from_school_year IS NULL OR applies_from_school_year REGEXP '^[0-9]{4}/[0-9]{4}$'
        )");
    }
};
