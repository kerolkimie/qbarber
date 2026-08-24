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

        $plans = \App\Models\SubscriptionPlan::where('status', 'active')->orderBy('price')->get();

        return view('admin.owners.show', compact('owner', 'plans'));
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

    /**
     * Bagi TEMPOH PERCUBAAN kepada owner — admin pilih pakej untuk "dipinjam"
     * ciri dia (had cawangan/kerusi) + bilangan hari, TANPA bayaran. Owner
     * boleh guna sistem penuh sepanjang tempoh ni.
     * Route: POST /admin/owners/{owner}/grant-trial
     */
    public function grantTrial(Request $request, Owner $owner)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'days' => 'required|integer|min:1|max:90',
        ]);

        $days = (int) $validated['days'];
        $plan = \App\Models\SubscriptionPlan::findOrFail($validated['plan_id']);

        // Kalau owner ada subscription aktif sedia ada, jangan overlap — tandakan
        // yang lama sebagai 'expired' dulu supaya tak konflik dengan trial baru.
        $owner->activeSubscription?->update(['status' => 'expired']);

        $subscription = \App\Models\Subscription::create([
            'owner_id' => $owner->id,
            'plan_id' => $plan->id,
            'agent_id' => $owner->agent_id,
            'start_date' => today(),
            'end_date' => today()->addDays($days),
            'amount_paid' => 0,
            'status' => 'active',
            'is_trial' => true,
        ]);

        \App\Models\ActivityLog::record(
            'trial_granted',
            "Admin bagi tempoh percubaan {$days} hari (pakej {$plan->name}) kepada \"{$owner->business_name}\", tamat pada " . $subscription->end_date->format('d M Y'),
            $subscription,
            ['plan' => $plan->name, 'days' => $days]
        );

        return redirect()->route('admin.owners.show', $owner)
            ->with('success', "Tempoh percubaan {$days} hari (pakej {$plan->name}) berjaya diberikan kepada {$owner->business_name}.");
    }
}
