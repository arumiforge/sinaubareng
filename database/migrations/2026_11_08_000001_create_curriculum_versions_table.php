<?php

declare(strict_types=1);

use App\Enums\CurriculumVersionStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_versions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name');
            $table->string('status')->comment('draft until Tim Validasi SIGRAK signs the data off (D-113)');
            $table->json('meta')->comment('Imported reference blob per source file: {files: {path: {sha256, source, …}}}. KM-SD-2026-R2 has two files');
            $table->timestamp('imported_at');
            $table->timestamps();
        });

        $statuses = implode("','", array_column(CurriculumVersionStatus::cases(), 'value'));
        DB::statement("ALTER TABLE curriculum_versions ADD CONSTRAINT curriculum_versions_status_check CHECK (status IN ('{$statuses}'))");
    }
};
