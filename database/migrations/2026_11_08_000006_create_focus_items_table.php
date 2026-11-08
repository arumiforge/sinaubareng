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
            $table->string('allocation_status', 32)->comment('As printed in the source, e.g. sesuai');
            $table->timestamps();
            $table->unique(['topic_id', 'order']);
        });
    }
};
