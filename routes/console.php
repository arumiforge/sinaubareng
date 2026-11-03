<?php

declare(strict_types=1);

use App\Enums\StaffRole;
use App\Models\StaffUser;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

// Creates a staff console account, e.g. the first admin on a new server.
Artisan::command('staff:create {email} {--name=} {--role=admin}', function (string $email): int {
    $data = [
        'email' => $email,
        'name' => $this->option('name') ?: $this->ask('Name'),
        'role' => $this->option('role'),
        'password' => $this->secret('Password (min. 12 characters)'),
    ];

    $validator = Validator::make($data, [
        'email' => ['required', 'email', 'max:255', 'unique:staff_users,email'],
        'name' => ['required', 'string', 'max:255'],
        'role' => ['required', 'in:'.implode(',', array_column(StaffRole::cases(), 'value'))],
        'password' => ['required', Password::min(12)],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    StaffUser::query()->create($validator->validated());
    $this->info("Staff account {$email} created.");

    return 0;
})->purpose('Create a staff console account');
