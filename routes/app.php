<?php

declare(strict_types=1);

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\PasswordController;
use App\Http\Controllers\Account\RoleSwitchController;
use App\Http\Controllers\Auth\InvitationAcceptanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\School\InvitationController;
use App\Http\Controllers\School\TeacherController;
use Illuminate\Support\Facades\Route;

// School app (app.<domain>, `web` guard). Paths are Indonesian, names English (D-149).
// Only sign-in, password reset, and invitation acceptance work without a session (D-010);
// tests/Feature/Auth/SessionRequiredTest.php fails when another route is left open.

Route::middleware(['guest', 'translations:auth'])->group(function (): void {
    Route::get('/masuk', [LoginController::class, 'create'])->name('login');
    Route::post('/masuk', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::get('/lupa-kata-sandi', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/lupa-kata-sandi', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.email');
    Route::get('/kata-sandi-baru/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/kata-sandi-baru', [NewPasswordController::class, 'store'])
        ->middleware('throttle:password-reset')
        ->name('password.store');
});

Route::middleware('translations:auth')->group(function (): void {
    Route::get('/undangan/{invitation}', [InvitationAcceptanceController::class, 'show'])->name('invitations.accept');
    Route::post('/undangan/{invitation}', [InvitationAcceptanceController::class, 'store'])
        ->middleware('throttle:invitation')
        ->name('invitations.accept.store');
});

Route::middleware('auth')->group(function (): void {
    Route::inertia('/', 'Home')->name('home');
    Route::post('/keluar', [LoginController::class, 'destroy'])->name('logout');
    Route::put('/ganti-peran', [RoleSwitchController::class, 'update'])->name('role.update');

    Route::middleware('translations:account')->group(function (): void {
        Route::get('/akun', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('/akun', [AccountController::class, 'update'])->name('account.update');
        Route::put('/akun/kata-sandi', [PasswordController::class, 'update'])->name('account.password.update');
    });

    // Operator portal.
    Route::middleware('translations:school')->group(function (): void {
        Route::get('/guru', [TeacherController::class, 'index'])->name('teachers.index');
        Route::post('/guru/undangan', [InvitationController::class, 'store'])->name('invitations.store');
        Route::post('/guru/undangan/{invitation}/kirim-ulang', [InvitationController::class, 'resend'])->name('invitations.resend');
        Route::delete('/guru/undangan/{invitation}', [InvitationController::class, 'destroy'])->name('invitations.destroy');
    });
});
