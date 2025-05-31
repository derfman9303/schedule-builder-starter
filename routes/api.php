<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::group(['prefix' => 'employees'], function () {
        Route::post('/', [EmployeeController::class, 'create'], function ($employee) {
            return redirect()->route('employees.index', ['employee' => $employee]);
        })->name('employees.create');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
