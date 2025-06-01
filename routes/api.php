<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::group(['prefix' => 'employees'], function () {
        Route::get('/', [EmployeeController::class, 'list'])->name('employees.list');

        Route::get('/{employee}', [EmployeeController::class, 'details'])->name('employee.details');

        Route::post('/{employee}', [EmployeeController::class, 'update'])->name('employee.update');

        Route::post('/', [EmployeeController::class, 'create'])->name('employees.create');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
