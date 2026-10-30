<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Portal\CourseController;
use App\Http\Controllers\Portal\DepartmentController;
use App\Http\Controllers\Portal\DesignationController;
use App\Http\Controllers\Portal\ProfileController;
use App\Http\Controllers\Portal\SubCourseController;
use App\Http\Controllers\Portal\SubjectController;
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
        Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
            Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
            Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
        });
        Route::get('/designations', [DesignationController::class, 'index'])->name('designations');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/designations', [DesignationController::class, 'store'])->name('designations.store');
            Route::put('/designations/{designation}', [DesignationController::class, 'update'])->name('designations.update');
            Route::delete('/designations/{designation}', [DesignationController::class, 'destroy'])->name('designations.destroy');
        });
        Route::get('/courses', [CourseController::class, 'index'])->name('courses');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
            Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
            Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
            Route::post('/sub-courses', [SubCourseController::class, 'store'])->name('sub-courses.store');
            Route::put('/sub-courses/{subCourse}', [SubCourseController::class, 'update'])->name('sub-courses.update');
            Route::delete('/sub-courses/{subCourse}', [SubCourseController::class, 'destroy'])->name('sub-courses.destroy');
        });
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers');
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
            Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
            Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        });
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::middleware('throttle:30,1')->group(function () {
            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
            Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
        });
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:profile-password')->name('profile.password');

        if ($portal === 'admin') {
            Route::get('/users', [UserController::class, 'index'])->name('users');
            Route::post('/users', [UserController::class, 'store'])->middleware('throttle:10,1')->name('users.store');
            Route::view('/active-users', 'portal.active-users', $shared + ['users' => []])->name('active-users');
        }
    });
}
