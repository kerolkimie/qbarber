<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Rules\MalaysianPhone;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    /**
     * Terima borang "Hubungi Kami" dari landing page (awam, tiada login).
     * Route: POST /hubungi-kami
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['required', 'string', new MalaysianPhone()],
            'email' => 'nullable|email|max:255',
            'business_name' => 'nullable|string|max:150',
            'message' => 'nullable|string|max:1000',
        ]);

        ContactInquiry::create($validated);

        return back()->with('success', 'Terima kasih! Kami akan hubungi anda tidak lama lagi.');
    }
}
