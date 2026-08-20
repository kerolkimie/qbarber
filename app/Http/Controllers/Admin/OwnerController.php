<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index()
    {
        $owners = Owner::with(['user', 'agent.user', 'activeSubscription.plan'])
            ->withCount('branches')
            ->latest()
            ->get();

        return view('admin.owners.index', compact('owners'));
    }

    public function show(Owner $owner)
    {
        $owner->load([
            'user', 'agent.user',
            'branches.barbers', 'branches.services',
            'subscriptions.plan', 'subscriptions.payments',
        ]);

        return view('admin.owners.show', compact('owner'));
    }

    /**
     * Tukar mod perbaharui pakej owner: online (ToyyibPay) atau offline (manual).
     * Route: POST /admin/owners/{owner}/renewal-mode
     */
    public function updateRenewalMode(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'renewal_mode' => 'required|in:online,offline',
        ]);

        $owner->update(['renewal_mode' => $validated['renewal_mode']]);

        $label = $validated['renewal_mode'] === 'online' ? 'Online (ToyyibPay)' : 'Offline (Manual)';

        return back()->with('success', "Mod perbaharui pakej untuk \"{$owner->business_name}\" ditukar kepada {$label}.");
    }

    /**
     * Aktifkan akaun owner secara MANUAL (tanpa perlu klik pautan emel) —
     * berguna kalau owner ada masalah terima emel, atau admin nak bantu terus.
     * Route: POST /admin/owners/{owner}/activate
     */
    public function activate(Owner $owner)
    {
        $user = $owner->user;

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Akaun owner ni dah aktif pun.');
        }

        $user->markEmailAsVerified();

        \App\Models\ActivityLog::record('account_activated_manual', "Admin aktifkan akaun \"{$owner->business_name}\" secara manual", $owner);

        return back()->with('success', 'Akaun "' . $owner->business_name . '" berjaya diaktifkan secara manual.');
    }
}
