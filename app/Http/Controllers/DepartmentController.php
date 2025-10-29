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
}
