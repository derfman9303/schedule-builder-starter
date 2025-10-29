<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::group(['prefix' => 'employees'], function () {
        Route::get('/', [EmployeeController::class, 'list'])->name('employees.list');
        Route::get('/{employee}', [EmployeeController::class, 'details'])->name('employee.details');

        Route::post('/', [EmployeeController::class, 'create'])->name('employees.create');
        Route::put('/{employee}', [EmployeeController::class, 'update'])->name('employee.update');

        Route::delete('/{employee}', [EmployeeController::class, 'delete'])->name('employee.delete');
    });

    Route::group(['prefix' => 'schedules'], function () {
        Route::get('', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::post('', [ScheduleController::class, 'store'])->name('schedules.store');

        Route::group(['prefix' => '{schedule}'], function () {
            Route::get('', [ScheduleController::class, 'show'])->name('schedules.show');
            Route::get('export', [ScheduleController::class, 'export'])->name('schedules.export');
            Route::put('', [ScheduleController::class, 'update'])->name('schedules.update');
            Route::delete('', [ScheduleController::class, 'delete'])->name('schedules.delete');
        });
    });

    Route::group(['prefix' => 'departments'], function () {
        Route::get('', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('', [DepartmentController::class, 'store'])->name('departments.store');
        Route::delete('{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
