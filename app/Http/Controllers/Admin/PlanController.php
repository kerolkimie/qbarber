<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->orderBy('price')->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:subscription_plans,name',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_branches' => 'required|integer|min:1',
            'max_barbers' => 'required|integer|min:1',
            'is_per_branch_limit' => 'nullable|boolean',
            'features' => 'nullable|string',
        ]);

        $validated['is_per_branch_limit'] = $request->boolean('is_per_branch_limit');
        $validated['points_included'] = 0;
        $validated['status'] = 'active';

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Pakej "' . $validated['name'] . '" berjaya ditambah.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:subscription_plans,name,' . $plan->id,
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_branches' => 'required|integer|min:1',
            'max_barbers' => 'required|integer|min:1',
            'is_per_branch_limit' => 'nullable|boolean',
            'features' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['is_per_branch_limit'] = $request->boolean('is_per_branch_limit');

        $plan->update($validated);

        return redirect()->route('admin.plans.index')->with('success', 'Pakej dikemaskini.');
    }

    /**
     * Padam terus KECUALI pakej tu dah pernah digunakan dalam subscription sebenar
     * (dalam kes tu nyahaktifkan sahaja, elak rosakkan rekod sejarah).
     */
    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            $plan->update(['status' => 'inactive']);

            return back()->with('success', 'Pakej ni pernah digunakan owner — dinyahaktifkan sahaja (bukan dipadam).');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')->with('success', 'Pakej dipadam.');
    }
}
