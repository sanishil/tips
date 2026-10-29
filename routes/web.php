<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Portal\DepartmentController;
use App\Http\Controllers\Portal\DesignationController;
use App\Http\Controllers\Portal\TeacherController;
use Illuminate\Support\Facades\Route;

/*
| Sign-in, sign-up, password reset and user accounts are real. The cover page and the
| other portal pages have no data source yet, so they get empty lists and show their
| "nothing yet" states.
*/

Route::view('/', 'cover.form', [
    'departments' => [],
    'subjects' => [],
    'teachers' => [],
    'options' => [],
])->middleware('password.changed')->name('cover.form');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:send-code');
    Route::get('/register/verify', [RegisterController::class, 'showVerify'])->name('register.verify');
    Route::post('/register/verify', [RegisterController::class, 'verify'])->middleware('throttle:verify-code');
    Route::post('/register/resend', [RegisterController::class, 'resend'])->middleware('throttle:resend-code')->name('register.resend');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->middleware('throttle:send-code');
    Route::get('/forgot-password/verify', [ForgotPasswordController::class, 'showVerify'])->name('password.verify');
    Route::post('/forgot-password/verify', [ForgotPasswordController::class, 'verify'])->middleware('throttle:verify-code');
    Route::post('/forgot-password/resend', [ForgotPasswordController::class, 'resend'])->middleware('throttle:resend-code')->name('password.resend');
    Route::get('/reset-password', [ForgotPasswordController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'update'])->middleware('throttle:new-password');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/change-password', [ChangePasswordController::class, 'edit'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->middleware('throttle:new-password');
});

foreach (['admin', 'tech'] as $portal) {
    Route::prefix($portal)->name($portal.'.')->middleware(['auth', 'password.changed', 'role:'.$portal])->group(function () use ($portal) {
        $shared = ['portal' => $portal, 'options' => [], 'departments' => []];

        Route::view('/', 'portal.dashboard', $shared + ['todayPrints' => []])->name('dashboard');
        Route::view('/students', 'portal.students', $shared + ['students' => []])->name('students');
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
            Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
            Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        });
        Route::view('/subjects', 'portal.subjects', $shared + ['subjects' => []])->name('subjects');
        Route::get('/designations', [DesignationController::class, 'index'])->name('designations');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/designations', [DesignationController::class, 'store'])->name('designations.store');
            Route::put('/designations/{designation}', [DesignationController::class, 'update'])->name('designations.update');
            Route::delete('/designations/{designation}', [DesignationController::class, 'destroy'])->name('designations.destroy');
        });
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
            Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
            Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        });
        Route::view('/profile', 'portal.profile', $shared)->name('profile');

        if ($portal === 'admin') {
            Route::get('/users', [UserController::class, 'index'])->name('users');
            Route::post('/users', [UserController::class, 'store'])->middleware('throttle:10,1')->name('users.store');
            Route::view('/active-users', 'portal.active-users', $shared + ['users' => []])->name('active-users');
        }
    });
}
