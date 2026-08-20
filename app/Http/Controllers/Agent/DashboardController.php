<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Owner;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard ejen: senarai owner yang direferral + jumlah komisen.
     * Route: GET /agent/dashboard
     */
    public function index()
    {
        $agent = Auth::user()->agent;

        $referredOwners = Owner::where('agent_id', $agent->id)
            ->with(['user', 'activeSubscription.plan'])
            ->latest()
            ->get();

        // Owner yang subscription dah tamat ATAU akan tamat dalam 7 hari — perlu
        // dihubungi ejen untuk perbaharui.
        $expiringOwners = $referredOwners->filter(function ($owner) {
            $sub = $owner->activeSubscription;

            return ! $sub || today()->diffInDays($sub->end_date, false) <= 7;
        });

        $commissionPaid = Commission::where('agent_id', $agent->id)
            ->where('status', 'paid')
            ->sum('amount');

        $commissionPending = Commission::where('agent_id', $agent->id)
            ->where('status', 'pending')
            ->sum('amount');

        return view('agent.dashboard', compact(
            'agent', 'referredOwners', 'expiringOwners', 'commissionPaid', 'commissionPending'
        ));
    }
}
