<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarberOverviewController extends Controller
{
    /**
     * Senarai SEMUA tukang gunting merentasi semua cawangan owner ni, boleh cari.
     * Route: GET /owner/tukang-gunting
     */
    public function index(Request $request)
    {
        $owner = Auth::user()->owner;
        $branchIds = $owner->branches()->pluck('id');
        $q = $request->input('q');

        $barbers = Barber::with(['branch', 'user'])
            ->whereIn('branch_id', $branchIds)
            ->when($q, fn ($query) => $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhereHas('branch', fn ($b) => $b->where('name', 'like', "%{$q}%"));
            }))
            ->orderBy('name')
            ->get();

        return view('owner.all-barbers.index', compact('barbers', 'q'));
    }
}
