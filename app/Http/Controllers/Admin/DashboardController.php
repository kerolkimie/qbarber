<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Branch;
use App\Models\Commission;
use App\Models\Owner;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_owners' => Owner::count(),
            'total_agents' => Agent::count(),
            'total_branches' => Branch::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_revenue' => Subscription::where('status', 'active')->sum('amount_paid'),
            'pending_commissions' => Commission::where('status', 'pending')->sum('amount'),
        ];

        $recentOwners = Owner::with(['user', 'agent'])->latest()->limit(6)->get();
        $recentSubscriptions = Subscription::with(['owner.user', 'plan'])->latest()->limit(6)->get();

        return view('admin.dashboard', compact('stats', 'recentOwners', 'recentSubscriptions'));
    }
}
