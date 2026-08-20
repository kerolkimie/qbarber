<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TopupPackage;
use Illuminate\Http\Request;

class TopupPackageController extends Controller
{
    public function index()
    {
        $packages = TopupPackage::withCount('pointBatches')->orderBy('points')->get();

        return view('admin.topup-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.topup-packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['status'] = 'active';

        TopupPackage::create($validated);

        return redirect()->route('admin.topup-packages.index')->with('success', 'Pakej topup ditambah.');
    }

    public function edit(TopupPackage $topupPackage)
    {
        return view('admin.topup-packages.edit', ['package' => $topupPackage]);
    }

    public function update(Request $request, TopupPackage $topupPackage)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $topupPackage->update($validated);

        return redirect()->route('admin.topup-packages.index')->with('success', 'Pakej topup dikemaskini.');
    }

    /**
     * Nyahaktifkan sahaja kalau dah pernah digunakan (elak rosakkan sejarah rekod),
     * padam terus kalau belum pernah digunakan.
     */
    public function destroy(TopupPackage $topupPackage)
    {
        if ($topupPackage->pointBatches()->exists()) {
            $topupPackage->update(['status' => 'inactive']);

            return back()->with('success', 'Pakej ni pernah digunakan owner — dinyahaktifkan sahaja.');
        }

        $topupPackage->delete();

        return redirect()->route('admin.topup-packages.index')->with('success', 'Pakej topup dipadam.');
    }
}
