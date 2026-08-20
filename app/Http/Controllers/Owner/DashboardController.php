<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\QueueTicket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::user()->owner;

        $branches = $owner->branches()->withCount(['barbers', 'services'])->get();
        $branchIds = $branches->pluck('id');

        $todayServed = QueueTicket::whereIn('branch_id', $branchIds)
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        $todayWaiting = QueueTicket::whereIn('branch_id', $branchIds)
            ->where('status', 'waiting')
            ->count();

        // Senarai preview (5 terkini) untuk papar terus dalam dashboard — sekali
        // dengan nama tukang gunting.
        $recentServed = QueueTicket::with(['service', 'barber', 'branch'])
            ->whereIn('branch_id', $branchIds)
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get();

        $recentWaiting = QueueTicket::with(['service', 'barber', 'branch'])
            ->whereIn('branch_id', $branchIds)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        $subscription = $owner->activeSubscription;
        $subscriptionValid = $owner->hasValidSubscription();
        $branchLimit = $owner->branchLimit();
        $chairLimit = $owner->chairLimit();
        $chairUsed = $owner->totalActiveBarbers();
        $isPerBranchChairLimit = $owner->isPerBranchChairLimit();

        return view('owner.dashboard', compact(
            'owner', 'branches', 'todayServed', 'todayWaiting', 'recentServed', 'recentWaiting',
            'subscription', 'subscriptionValid', 'branchLimit', 'chairLimit', 'chairUsed', 'isPerBranchChairLimit'
        ));
    }
}
