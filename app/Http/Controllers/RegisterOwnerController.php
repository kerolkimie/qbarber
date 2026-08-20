<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Branch;
use App\Models\Owner;
use App\Models\Role;
use App\Models\User;
use App\Rules\MalaysianPhone;
use App\Rules\StrongPassword;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterOwnerController extends Controller
{
    /**
     * Papar borang pendaftaran barbershop.
     * Route: GET /daftar
     */
    public function create(Request $request)
    {
        // Kalau datang dari halaman pricing (?plan=Professional), papar sebagai makluman sahaja.
        $selectedPlan = $request->query('plan');

        return view('auth.register-owner', compact('selectedPlan'));
    }

    /**
     * Proses pendaftaran: cipta user (role=owner) + rekod owner.
     * Kalau agent_code sah, owner akan dikaitkan dengan agent tersebut (untuk komisen kelak).
     * Route: POST /daftar
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:150',
            'name' => 'required|string|max:100',
            'email' => 'required|string|email:rfc,filter|max:255|unique:users,email',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'password' => ['required', 'confirmed', new StrongPassword()],
            'agent_code' => 'nullable|string',
        ]);

        $ownerRole = Role::firstOrCreate(['name' => 'owner']);

        $user = User::create([
            'role_id' => $ownerRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        // Cari agent ikut kod (kalau owner masukkan). Jika tiada/tidak sah, owner daftar terus
        // tanpa agent — bermakna tiada komisen akan dijana (ikut spec asal).
        $agent = null;
        if (! empty($validated['agent_code'])) {
            $agent = Agent::where('agent_code', $validated['agent_code'])
                ->where('status', 'active')
                ->first();
        }

        $owner = Owner::create([
            'user_id' => $user->id,
            'agent_id' => $agent?->id,
            'business_name' => $validated['business_name'],
            'status' => 'active',
        ]);

        // Auto-cipta SATU cawangan default guna nama perniagaan — owner dengan
        // 1 lokasi je tak perlu langkah "Tambah Cawangan" berasingan. Kalau
        // owner memang ada >1 kedai, dia boleh tambah cawangan lain bila-bila
        // masa dari dashboard.
        Branch::create([
            'owner_id' => $owner->id,
            'name' => $validated['business_name'],
            'phone' => $validated['phone'] ?? null,
            'commission_percent' => 40,
            'status' => 'active',
        ]);

        // Trigger emel pengesahan akaun (Laravel/Breeze hantar automatik sebab
        // User model implement MustVerifyEmail).
        event(new Registered($user));

        \App\Models\ActivityLog::record('email_sent', "Emel pengaktifan dihantar kepada {$user->email} (daftar sendiri: {$owner->business_name})", $user);

        // TAK auto log masuk — bawa terus ke page login dengan notis jelas
        // supaya owner tahu perlu semak emel untuk aktifkan akaun dulu.
        return redirect()->route('login')
            ->with('success', 'Pendaftaran berjaya! Emel pengaktifan telah dihantar — sila rujuk emel yang didaftarkan (' . $user->email . ') dan klik pautan pengaktifan sebelum log masuk.');
    }
}
