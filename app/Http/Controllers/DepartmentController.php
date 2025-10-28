<?php

namespace App\Http\Controllers;

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
