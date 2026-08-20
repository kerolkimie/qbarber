<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('owner')
            ->withCount(['barbers', 'services'])
            ->latest()
            ->get();

        return view('admin.branches.index', compact('branches'));
    }
}
