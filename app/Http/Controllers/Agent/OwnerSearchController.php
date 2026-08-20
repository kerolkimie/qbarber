<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerSearchController extends Controller
{
    /**
     * Carian owner bawah ejen ni — senarai lengkap dengan status subscription,
     * pakej, tarikh tamat, dan no. telefon (untuk senang ejen hubungi bila
     * point owner tu dah hampir habis).
     * Route: GET /agent/owners
     */
    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        $search = $request->input('q');

        $owners = Owner::where('agent_id', $agent->id)
            ->with(['user', 'activeSubscription.plan'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('business_name', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->get()
            ->map(function ($owner) {
                $sub = $owner->activeSubscription;
                $owner->subscription_expiring = ! $sub || today()->diffInDays($sub->end_date, false) <= 7;

                return $owner;
            });

        return view('agent.owners.index', compact('owners', 'search'));
    }
}
