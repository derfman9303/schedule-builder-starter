<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function index(): Collection
    {
        return Department::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = Department::create([
            'user_id' => auth()->id(),
            'name' => $request->validated('name'),
        ]);

        return response()->json($department, 201);
    }

    public function show(Department $department): JsonResponse
    {
        // Ensure the user can only view their own departments
        if ($department->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($department);
    }

    public function update(StoreDepartmentRequest $request, Department $department): JsonResponse
    {
        // Ensure the user can only update their own departments
        if ($department->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $department->update([
            'name' => $request->validated('name'),
        ]);

        return response()->json($department);
    }

    public function destroy(Department $department): JsonResponse
    {
        // Ensure the user can only delete their own departments
        if ($department->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
