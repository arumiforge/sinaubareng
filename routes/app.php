<?php

declare(strict_types=1);

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\PasswordController;
use App\Http\Controllers\Account\RoleSwitchController;
use App\Http\Controllers\Auth\InvitationAcceptanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\School\AcademicYearController;
use App\Http\Controllers\School\CalendarController;
use App\Http\Controllers\School\InvitationController;
use App\Http\Controllers\School\SchoolClassController;
use App\Http\Controllers\School\TeacherController;
use App\Http\Controllers\School\TeachingAssignmentController;
use App\Http\Controllers\School\TimetableController;
use App\Http\Controllers\School\TimetableDraftController;
use App\Http\Controllers\Teacher\MyTimetableController;
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

    Route::middleware('translations:classes')->group(function (): void {
        // /tahun-ajaran, /tahun-ajaran/buat, /tahun-ajaran/{academicYear}/ubah
        Route::resource('tahun-ajaran', AcademicYearController::class)
            ->except('show')
            ->parameters(['tahun-ajaran' => 'academicYear'])
            ->names('academic_years');
        Route::post('/tahun-ajaran/{academicYear}/salin-kelas', [AcademicYearController::class, 'copyClasses'])->name('academic_years.copy_classes');
        // /kelas, /kelas/buat, /kelas/{class}, /kelas/{class}/ubah
        Route::resource('kelas', SchoolClassController::class)
            ->parameters(['kelas' => 'class'])
            ->names('classes');
        Route::put('/kelas/{class}/guru-mapel', [TeachingAssignmentController::class, 'update'])->name('classes.assignments.update');
    });

    Route::middleware('translations:timetable')->group(function (): void {
        // Operator: /kelas/{class}/jadwal; a row belongs to its class (scoped binding).
        Route::get('/kelas/{class}/jadwal', [TimetableController::class, 'show'])->name('classes.timetable.show');
        Route::post('/kelas/{class}/jadwal', [TimetableController::class, 'store'])->name('classes.timetable.store');
        Route::scopeBindings()->group(function (): void {
            Route::put('/kelas/{class}/jadwal/{timetableSlot}', [TimetableController::class, 'update'])->name('classes.timetable.update');
            Route::delete('/kelas/{class}/jadwal/{timetableSlot}', [TimetableController::class, 'destroy'])->name('classes.timetable.destroy');
            Route::put('/kelas/{class}/jadwal/{timetableSlot}/kunci', [TimetableController::class, 'lock'])->name('classes.timetable.lock');
        });

        // Operator: Susun draf jadwal (D-262).
        Route::get('/jadwal/susun-draf', [TimetableDraftController::class, 'show'])->name('timetable.draft.show');
        Route::post('/jadwal/susun-draf', [TimetableDraftController::class, 'store'])->name('timetable.draft.store');
        Route::put('/jadwal/susun-draf/pengaturan', [TimetableDraftController::class, 'settings'])->name('timetable.draft.settings');
        Route::post('/jadwal/susun-draf/{draft}/simpan', [TimetableDraftController::class, 'save'])->name('timetable.draft.save');

        // Teacher: Kelas Saya > Jadwal.
        Route::get('/kelas-saya/jadwal', MyTimetableController::class)->name('my_classes.timetable');
    });

    // Kalender (D-253…D-261): every member reads it; the operator manages it; a wali kelas adds events for their class.
    Route::middleware('translations:calendar')->group(function (): void {
        Route::get('/kalender', [CalendarController::class, 'show'])->name('calendar.show');
        Route::post('/kalender', [CalendarController::class, 'store'])->name('calendar.store');
        Route::post('/kalender/{calendar}/perubahan-wilayah', [CalendarController::class, 'regionChanges'])->name('calendar.region_changes');
        Route::put('/kalender/{calendar}/cuti-bersama', [CalendarController::class, 'jointLeave'])->name('calendar.joint_leave');
        Route::post('/kalender/{calendar}/acara', [CalendarController::class, 'storeEvent'])->name('calendar.events.store');
        Route::scopeBindings()->group(function (): void {
            Route::put('/kalender/{calendar}/acara/{event}', [CalendarController::class, 'updateEvent'])->name('calendar.events.update');
            Route::delete('/kalender/{calendar}/acara/{event}', [CalendarController::class, 'destroyEvent'])->name('calendar.events.destroy');
        });
    });
});
