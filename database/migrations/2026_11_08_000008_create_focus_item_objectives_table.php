<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Which TP a fokus serves (finding 3 of the EDUNEXA mapping doc).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('focus_item_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('focus_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_objective_id')->constrained()->cascadeOnDelete();
            $table->unique(['focus_item_id', 'learning_objective_id'], 'focus_item_objectives_pair_unique');
            $table->index('learning_objective_id');
        });
    }
};
