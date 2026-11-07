<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// The role switch sends a membership in its request and lists memberships in props,
// so memberships get a public id like other rows that leave the server (DB-11, D-182).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_memberships', function (Blueprint $table) {
            $table->uuid('public_id')->nullable()->after('id');
        });

        DB::table('school_memberships')->whereNull('public_id')->orderBy('id')->each(function (object $row): void {
            DB::table('school_memberships')->where('id', $row->id)->update(['public_id' => (string) Str::uuid7()]);
        });

        Schema::table('school_memberships', function (Blueprint $table) {
            $table->uuid('public_id')->nullable(false)->change();
            $table->unique('public_id');
        });
    }
};
