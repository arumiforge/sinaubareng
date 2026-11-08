<?php

declare(strict_types=1);

use App\Enums\DefaultTeacher;
use App\Enums\Religion;
use App\Enums\SubjectKind;
use App\Enums\SubjectSchoolLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)->unique();
            $table->string('name');
            $table->string('kind');
            $table->string('default_teacher');
            $table->string('religion')->nullable()->comment('NULL = not a religion subject split by the student\'s religion');
            $table->string('school_level');
            $table->json('aliases')->comment('Names and keys the source files use for this subject');
            $table->timestamps();
        });

        foreach ([
            'kind' => SubjectKind::class,
            'default_teacher' => DefaultTeacher::class,
            'religion' => Religion::class,
            'school_level' => SubjectSchoolLevel::class,
        ] as $column => $enum) {
            $values = implode("','", array_column($enum::cases(), 'value'));
            DB::statement("ALTER TABLE subjects ADD CONSTRAINT subjects_{$column}_check CHECK ({$column} IN ('{$values}'))");
        }
    }
};
