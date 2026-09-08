<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Route: GET /admin/settings
     */
    public function edit()
    {
        $whatsappNumber = Setting::get('whatsapp_number');

        return view('admin.settings.edit', compact('whatsappNumber'));
    }

    /**
     * Route: POST /admin/settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_number' => 'nullable|string|max:20',
        ]);

        Setting::set('whatsapp_number', $validated['whatsapp_number'] ?? null);

        return back()->with('success', 'Tetapan berjaya dikemaskini.');
    }
}
