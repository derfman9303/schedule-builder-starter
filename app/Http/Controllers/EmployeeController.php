<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class EmployeeController extends Controller
{
    /**
     * Handle the incoming request to create a new employee.
     */
    public function create(Request $request): Employee
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $validatedData['user_id'] = Auth::id();

        $employee = Employee::create($validatedData);

        return $employee;
    }

    /**
     * Show the employee details.
     */
    public function list(): Collection
    {
        $employees = Auth::user()->employees;

        return $employees;
    }
}
