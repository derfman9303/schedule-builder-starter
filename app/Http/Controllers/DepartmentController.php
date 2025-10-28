<?php

namespace App\Http\Controllers;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index(): Collection
    {
        return Department::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
