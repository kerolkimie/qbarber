<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRedirectController extends Controller
{
    /**
     * Lepas login, agihkan pengguna ke dashboard yang betul ikut role.
     */
    public function __invoke(Request $request)
    {
        $roleName = $request->user()->role?->name;

        return match ($roleName) {
            'super_admin' => redirect()->route('admin.dashboard'),
            'barber' => redirect()->route('barber.dashboard'),
            'owner' => redirect()->route('owner.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            default => view('dashboard'),
        };
    }
}
