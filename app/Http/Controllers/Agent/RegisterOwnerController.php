<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use App\Rules\MalaysianPhone;
use App\Rules\StrongPassword;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterOwnerController extends Controller
{
    /**
     * Papar borang daftar barbershop baru (dari dashboard ejen).
     * Route: GET /agent/daftar-barbershop
     */
    public function create()
    {
        return view('agent.register-owner');
    }

    /**
     * Daftar barbershop baru — auto-kaitkan dengan ejen yang sedang log masuk
     * (tak perlu masukkan kod referral, sebab ejen sendiri yang daftar).
     * Route: POST /agent/daftar-barbershop
     */
    public function store(Request $request)
    {
        $agent = Auth::user()->agent;

        $validated = $request->validate([
            'business_name' => 'required|string|max:150',
            'name' => 'required|string|max:100',
            'email' => 'required|string|email:rfc,filter|max:255|unique:users,email',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'password' => ['required', 'confirmed', new StrongPassword()],
        ]);

        if (empty($validated['phone'])) {
            $validated['phone'] = null;
        }

        $ownerRole = Role::firstOrCreate(['name' => 'owner']);

        $user = User::create([
            'role_id' => $ownerRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $owner = Owner::create([
            'user_id' => $user->id,
            'agent_id' => $agent->id,
            'business_name' => $validated['business_name'],
            'status' => 'active',
        ]);

        // Auto-cipta satu cawangan default, sama macam flow daftar sendiri.
        Branch::create([
            'owner_id' => $owner->id,
            'name' => $validated['business_name'],
            'phone' => $validated['phone'],
            'commission_percent' => 40,
            'status' => 'active',
        ]);

        // Owner tetap terima emel pengesahan akaun sendiri — PENTING: kita TAK
        // Auth::login() akaun owner ni, sebab ejen sendiri yang sedang log masuk
        // dan tak patut ditukar sesi kepada akaun owner secara tak sengaja.
        event(new Registered($user));

        \App\Models\ActivityLog::record('email_sent', "Emel pengaktifan dihantar kepada {$user->email} (didaftarkan oleh ejen: {$agent->user->name})", $user);

        return redirect()->route('agent.dashboard')
            ->with('success', 'Barbershop "' . $owner->business_name . '" berjaya didaftarkan bawah akaun anda. Emel pengesahan telah dihantar kepada owner.');
    }
}
