<?php

declare(strict_types=1);

use App\Enums\Phase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// CP rows of the CP-only versions (D-226).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->restrictOnDelete();
            $table->char('phase', 1);
            $table->string('code', 96)->unique();
            $table->unsignedSmallInteger('order');
            $table->string('element')->nullable()->comment('NULL = the phase summary that covers every element');
            $table->string('sub_element')->nullable()->comment('NULL = the source prints no sub-element');
            $table->text('text');
            $table->json('extras')->nullable()->comment('lead_in, element_intro, arabic_passages, review_notes; NULL when the row has none');
            $table->timestamps();
            $table->unique(['curriculum_version_id', 'subject_id', 'phase', 'order'], 'learning_outcomes_order_unique');
            $table->index(['subject_id', 'phase']);
        });

        $phases = implode("','", array_column(Phase::cases(), 'value'));
        DB::statement("ALTER TABLE learning_outcomes ADD CONSTRAINT learning_outcomes_phase_check CHECK (phase IN ('{$phases}'))");
    }
};
