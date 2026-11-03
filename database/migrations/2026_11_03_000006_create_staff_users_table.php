<?php

declare(strict_types=1);

use App\Enums\StaffRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->rememberToken();
            $table->timestamps();
        });

        $roles = implode("','", array_column(StaffRole::cases(), 'value'));
        DB::statement("ALTER TABLE staff_users ADD CONSTRAINT staff_users_role_check CHECK (role IN ('{$roles}'))");
    }
};
