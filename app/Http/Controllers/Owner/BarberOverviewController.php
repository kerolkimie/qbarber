<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use Illuminate\Support\Facades\Auth;

class BarberOverviewController extends Controller
{
    /**
     * Senarai SEMUA tukang gunting merentasi semua cawangan owner ni.
     * Route: GET /owner/tukang-gunting
     */
    public function index()
    {
        $owner = Auth::user()->owner;
        $branchIds = $owner->branches()->pluck('id');

        $barbers = Barber::with(['branch', 'user'])
            ->whereIn('branch_id', $branchIds)
            ->orderBy('name')
            ->get();

        return view('owner.all-barbers.index', compact('barbers'));
    }
}
