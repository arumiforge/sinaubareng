<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// SIGRAK curriculum format (D-363, D-365): old codes live only in the team crosswalk,
// and the per-topic review notes are SIGRAK curation notes.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropColumn('legacy_codes');
            $table->renameColumn('audit', 'curation_notes');
        });

        Schema::table('topics', function (Blueprint $table) {
            $table->json('curation_notes')->nullable()->comment('Curation notes of the topic (D-364); NULL when there are none')->change();
            $table->json('extras')->nullable()->comment('sub_element, art branch, science process skills, ATP details; NULL when there are none')->change();
        });
    }
};
