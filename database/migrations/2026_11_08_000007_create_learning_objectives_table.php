<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tujuan pembelajaran (TP, EDUNEXA Data_Tujuan).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->string('code', 96)->unique();
            $table->unsignedSmallInteger('order');
            $table->text('text');
            $table->timestamps();
            $table->unique(['topic_id', 'order']);
        });
    }
};
