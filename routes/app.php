<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// School app (app.<domain>, `web` guard). Paths are Indonesian, names English (D-149).

Route::inertia('/', 'Home')->name('home');
