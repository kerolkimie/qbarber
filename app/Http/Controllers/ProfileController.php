<?php

namespace App\Http\Controllers;

use App\Rules\MalaysianPhone;
use App\Rules\StrongPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Papar borang kemaskini akaun — sama untuk semua role (owner/barber/agent/admin).
     * Route: GET /profile
     */
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /**
     * Kemaskini nama/emel/telefon. Kalau emel ditukar, akaun kena verify semula
     * demi keselamatan (elak orang tukar emel ke akaun lain tanpa kebenaran).
     * Route: PATCH /profile
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email:rfc,filter|max:255|unique:users,email,' . $user->id,
            'phone' => ['nullable', 'string', new MalaysianPhone()],
        ]);

        if (empty($validated['phone'])) {
            $validated['phone'] = null;
        }

        $emailChanged = $user->email !== $validated['email'];

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $newEmail = $user->email;
            $user->sendEmailVerificationNotification();
            \App\Models\ActivityLog::record('email_sent', "Emel pengesahan dihantar kepada {$newEmail} (tukar emel akaun)", $user);

            // Log keluar automatik — akaun kena verify emel baru dulu sebelum
            // boleh log masuk semula (konsisten dengan flow pendaftaran).
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Guna query string (bukan session flash) untuk notis — blok khas
            // di login.blade.php dah papar mesej berkaitan, elak duplicate.
            return redirect()->route('login', ['email_updated' => 1, 'email' => $newEmail]);
        }

        return redirect()->route('profile.edit')->with('success', 'Profil berjaya dikemaskini.');
    }

    /**
     * Tukar kata laluan (perlu sahkan kata laluan semasa dulu).
     * Route: PUT /profile/password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', new StrongPassword()],
        ]);

        Auth::user()->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('profile.edit')->with('success', 'Kata laluan berjaya ditukar.');
    }

    /**
     * Padam akaun sendiri (perlu sahkan kata laluan).
     * Route: DELETE /profile
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = Auth::user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'Akaun anda telah dipadam.');
    }
}
