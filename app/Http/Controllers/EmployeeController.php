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

    /**
     * Show the details of a specific employee.
     */
    public function details(Employee $employee): Employee
    {
        if ($employee->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $employee;
    }

    /**
     * Update the employee details.
     */
    public function update(Request $request, Employee $employee): Employee
    {
        if ($employee->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $employee->update($validatedData);

        return $employee;
    }
}
