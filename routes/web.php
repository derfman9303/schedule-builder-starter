<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::group(['prefix' => 'schedules'], function () {
        Route::get('/new', function () {
            return Inertia::render('schedules/NewSchedule');
        })->name('schedules.new');
    });

    Route::group(['prefix' => 'employees'], function () {
        Route::get('/', function () {
            return Inertia::render('employees/Employees');
        })->name('employees.index');

        Route::get('/add', function () {
            return Inertia::render('employees/AddEmployee');
        })->name('employees.add');

        Route::post('/', [EmployeeController::class, 'create'], function () {
            return redirect()->route('employees.index');
        })->name('employees.create');
    });

    Route::get('config', function () {
        return Inertia::render('Config');
    })->name('config');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
