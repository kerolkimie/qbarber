<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;

class CommissionController extends Controller
{
    /**
     * Senarai semua komisen ejen — admin boleh tandakan sebagai dibayar.
     * Route: GET /admin/commissions
     */
    public function index()
    {
        $commissions = Commission::with(['agent.user', 'subscription.owner', 'subscription.plan'])
            ->latest()
            ->get();

        $totalPending = $commissions->where('status', 'pending')->sum('amount');
        $totalPaid = $commissions->where('status', 'paid')->sum('amount');

        return view('admin.commissions.index', compact('commissions', 'totalPending', 'totalPaid'));
    }

    /**
     * Tandakan satu komisen sebagai dibayar.
     * Route: POST /admin/commissions/{commission}/mark-paid
     */
    public function markPaid(Commission $commission)
    {
        $commission->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $commission->load('agent.user');

        \App\Models\ActivityLog::record(
            'commission_paid',
            "Komisen RM{$commission->amount} kepada ejen \"{$commission->agent->user->name}\" ditandakan dibayar",
            $commission,
            ['amount' => $commission->amount, 'agent' => $commission->agent->user->name]
        );

        return back()->with('success', 'Komisen ditandakan sebagai dibayar.');
    }
}
