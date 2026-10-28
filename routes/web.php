<?php

use App\Support\DemoData;
use Illuminate\Support\Facades\Route;

/*
| Design-only routes. Every page renders static sample data from DemoData;
| controllers, auth middleware and form handlers come in the backend phase.
*/

Route::view('/', 'cover.form', [
    'departments' => DemoData::departments(),
    'subjects' => DemoData::subjects(),
    'teachers' => DemoData::teachers(),
    'options' => DemoData::options(),
])->name('cover.form');

Route::view('/login', 'auth.login')->name('login');
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');

foreach (['admin', 'tech'] as $portal) {
    Route::prefix($portal)->name($portal.'.')->group(function () use ($portal) {
        $shared = ['portal' => $portal, 'options' => DemoData::options(), 'departments' => DemoData::departments()];

        Route::view('/', 'portal.dashboard', $shared + ['todayPrints' => DemoData::todayPrints()])->name('dashboard');
        Route::view('/students', 'portal.students', $shared + ['students' => DemoData::students()])->name('students');
        Route::view('/departments', 'portal.departments', $shared)->name('departments');
        Route::view('/subjects', 'portal.subjects', $shared + ['subjects' => DemoData::subjects()])->name('subjects');
        Route::view('/teachers', 'portal.teachers', $shared + ['teachers' => DemoData::teachers()])->name('teachers');
        Route::view('/profile', 'portal.profile', $shared)->name('profile');

        if ($portal === 'admin') {
            Route::view('/users', 'portal.users', $shared + ['users' => DemoData::users()])->name('users');
            Route::view('/active-users', 'portal.active-users', $shared + ['users' => DemoData::users()])->name('active-users');
        }
    });
}
