<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

// Exam site (ujian.<domain>, `exam` guard). Never uses App\AI (D-027).

Route::inertia('/', 'Home')->name('home');
