<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    /**
     * Senarai semua pertanyaan dari borang "Hubungi Kami" landing page.
     * Route: GET /admin/inquiries
     */
    public function index(Request $request)
    {
        $status = $request->input('status');

        $inquiries = ContactInquiry::when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $newCount = ContactInquiry::where('status', 'new')->count();

        return view('admin.inquiries.index', compact('inquiries', 'status', 'newCount'));
    }

    /**
     * Tukar status pertanyaan (new/contacted/closed).
     * Route: POST /admin/inquiries/{inquiry}/status
     */
    public function updateStatus(Request $request, ContactInquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,closed',
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Status pertanyaan dikemaskini.');
    }
}
