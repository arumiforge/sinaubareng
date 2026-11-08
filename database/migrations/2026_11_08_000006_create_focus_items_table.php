<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Fokus materi (EDUNEXA Data_Fokus).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('focus_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->string('code', 96)->unique();
            $table->unsignedSmallInteger('order');
            $table->string('title');
            $table->unsignedSmallInteger('estimated_jp');
            $table->string('allocation_status')->comment('As printed in the source: a short word (sesuai) or a sentence (KM-SD-2026-R2)');
            $table->timestamps();
            $table->unique(['topic_id', 'order']);
        });
    }
};
