<?php

declare(strict_types=1);

use App\Enums\CalendarEventKind;
use App\Enums\ExamType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Events of a region or school calendar (data model, Calendar). `class_id` limits an
// event to one class (D-258); `exam_type` tells STS from SAS (D-260, D-269).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('calendar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->comment('Only this class (D-258)')->constrained()->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('kind', 16);
            $table->string('exam_type', 8)->nullable();
            $table->string('title', 120);
            $table->uuid('source_event_uid')->nullable()->comment('School copy of a region event: its public_id (D-256)');
            $table->timestamps();
            $table->index(['calendar_id', 'starts_on']);
        });

        $kinds = implode("','", array_column(CalendarEventKind::cases(), 'value'));
        $types = implode("','", array_column(ExamType::cases(), 'value'));
        DB::statement("ALTER TABLE calendar_events ADD CONSTRAINT calendar_events_kind_check CHECK (kind IN ('{$kinds}'))");
        DB::statement('ALTER TABLE calendar_events ADD CONSTRAINT calendar_events_dates_check CHECK (starts_on <= ends_on)');
        DB::statement("ALTER TABLE calendar_events ADD CONSTRAINT calendar_events_exam_type_check CHECK (
            (kind = 'exam' AND exam_type IS NOT NULL AND exam_type IN ('{$types}')) OR (kind <> 'exam' AND exam_type IS NULL)
        )");
    }
};
