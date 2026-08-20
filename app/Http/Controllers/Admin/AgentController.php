<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use App\Rules\MalaysianPhone;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    /**
     * Senarai semua ejen + bilangan owner yang mereka referral.
     * Route: GET /admin/agents
     */
    public function index()
    {
        $agents = Agent::with('user')->withCount('owners')->latest()->get();

        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        return view('admin.agents.create');
    }

    /**
     * Daftar ejen BARU — cipta akaun User (role=agent) + rekod Agent dengan
     * kod referral unik & peratus komisen yang admin tetapkan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email:rfc,filter|max:255|unique:users,email',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'password' => ['required', 'confirmed', new StrongPassword()],
            'commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        $agentRole = Role::firstOrCreate(['name' => 'agent']);

        $user = User::create([
            'role_id' => $agentRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            // Akaun dicipta terus oleh admin, tak perlu verify emel.
            'email_verified_at' => now(),
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'agent_code' => $this->generateUniqueCode(),
            'commission_percent' => $validated['commission_percent'],
            'status' => 'active',
        ]);

        return redirect()->route('admin.agents.index')
            ->with('success', 'Ejen "' . $validated['name'] . '" berjaya didaftarkan. Kod referral: ' . $agent->agent_code);
    }

    public function edit(Agent $agent)
    {
        return view('admin.agents.edit', compact('agent'));
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'commission_percent' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $agent->update([
            'commission_percent' => $validated['commission_percent'],
            'status' => $validated['status'],
        ]);

        $agent->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('admin.agents.index')->with('success', 'Maklumat ejen dikemaskini.');
    }

    /**
     * "Padam" = nyahaktifkan sahaja (bukan delete rekod terus) — owner yang
     * pernah didaftar melalui ejen ni kekal, cuma ejen tak boleh log masuk lagi.
     */
    public function destroy(Agent $agent)
    {
        $agent->update(['status' => 'inactive']);
        $agent->user->update(['status' => 'suspended']);

        return back()->with('success', 'Ejen "' . $agent->user->name . '" dinyahaktifkan.');
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (Agent::where('agent_code', $code)->exists());

        return $code;
    }
}
