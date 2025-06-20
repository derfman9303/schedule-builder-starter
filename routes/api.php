<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ScheduleController;

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::group(['prefix' => 'employees'], function () {
        Route::get('/', [EmployeeController::class, 'list'])->name('employees.list');
        Route::get('/{employee}', [EmployeeController::class, 'details'])->name('employee.details');

        Route::post('/', [EmployeeController::class, 'create'])->name('employees.create');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employee.update');

        Route::delete('/{employee}', [EmployeeController::class, 'delete'])->name('employee.delete');
    });

    Route::group(['prefix' => 'schedules'], function () {
        Route::post('', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::group(['prefix' => '{schedule}'], function () {
            Route::get('', [ScheduleController::class, 'show'])->name('schedules.show');
            Route::put('', [ScheduleController::class, 'update'])->name('schedules.update');
        });
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
