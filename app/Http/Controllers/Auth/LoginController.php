<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Override POST /login Breeze — tambah semakan "akaun belum aktif" SEBELUM
     * sesi disahkan penuh, supaya pengguna dapat notifikasi jelas terus di
     * page login (bukan senyap redirect ke page lain).
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Emel atau kata laluan tidak sah.',
            ]);
        }

        $user = Auth::user();

        if (! $user->hasVerifiedEmail()) {
            // Log keluar semula — jangan biar sesi separuh aktif untuk akaun
            // yang belum disahkan.
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // PENTING: guna redirect()->route('login') terus, BUKAN back() —
            // sebab session()->invalidate() di atas turut hapus rekod "url
            // sebelumnya" yang diperlukan back(), menyebabkan ia jatuh ke
            // page lain (cth. landing page) bukan kembali ke /login.
            return redirect()->route('login')
                ->withInput($request->only('email'))
                ->with('error', 'Akaun belum disahkan. Sila semak emel yang didaftarkan (' . $user->email . ') untuk pautan pengaktifan.')
                ->with('unverified_email', $user->email);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Hantar semula emel pengaktifan — boleh guna walaupun belum login (guest).
     * Mesej sentiasa sama tak kira emel wujud/tidak, elak scan emel berdaftar.
     */
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            \App\Models\ActivityLog::record('email_sent', "Hantar semula emel pengaktifan kepada {$user->email}", $user);
        }

        return back()->with('success', 'Jika emel tersebut berdaftar dan belum aktif, pautan pengaktifan baru telah dihantar.');
    }

    /**
     * Sahkan emel TERUS dari pautan (tak perlukan pengguna login dulu) — sebab
     * sistem kita tak auto-login lepas daftar, versi Breeze asal (yang perlukan
     * 'auth' middleware) akan buntu (pautan perlukan login, tapi tak boleh login
     * sebab belum verify). Route ni cari user terus dari {id} dalam URL.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Pautan pengesahan tidak sah.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')->with('success', 'Akaun anda sudah pun disahkan. Sila log masuk.');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return redirect()->route('login')
            ->with('success', 'Akaun anda berjaya diaktifkan! Sila log masuk untuk teruskan.');
    }

    /**
     * Override POST /forgot-password Breeze — semak status verified DULU sebelum
     * hantar pautan reset. Kalau akaun belum aktif, tak masuk akal nak reset
     * password akaun yang tak boleh log masuk pun — bagitahu perlu aktifkan dulu.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && ! $user->hasVerifiedEmail()) {
            return back()->withInput($request->only('email'))
                ->with('error', 'Akaun ini belum diaktifkan. Sila aktifkan akaun anda dahulu sebelum menetapkan semula kata laluan.')
                ->with('unverified_email', $user->email);
        }

        $status = \Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));

        if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT && $user) {
            \App\Models\ActivityLog::record('email_sent', "Emel tetapan semula kata laluan dihantar kepada {$user->email}", $user);
        }

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
