<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Rules\MalaysianPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerBranchController extends Controller
{
    public function index()
    {
        $owner = Auth::user()->owner;

        $branches = $owner->branches()
            ->withCount(['barbers', 'services'])
            ->latest()
            ->get();

        return view('owner.branches.index', compact('branches'));
    }

    public function create()
    {
        $owner = Auth::user()->owner;

        if (! $owner->canAddBranch()) {
            return redirect()->route('owner.branches.index')
                ->with('error', 'Pakej "' . ($owner->currentPlan()->name ?? 'semasa') . '" anda hanya benarkan ' . $owner->branchLimit() . ' cawangan. Sila upgrade pakej untuk tambah cawangan lagi.');
        }

        return view('owner.branches.create');
    }

    public function store(Request $request)
    {
        $owner = Auth::user()->owner;

        if (! $owner->canAddBranch()) {
            return redirect()->route('owner.branches.index')
                ->with('error', 'Pakej semasa anda hanya benarkan ' . $owner->branchLimit() . ' cawangan. Sila upgrade pakej untuk tambah cawangan lagi.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string|max:255',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        $branch = Branch::create([
            'owner_id' => $owner->id,
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'commission_percent' => $validated['commission_percent'],
            'status' => 'active',
        ]);

        return redirect()->route('owner.branches.index')
            ->with('success', 'Cawangan "' . $branch->name . '" berjaya didaftarkan. Kod QR: /q/' . $branch->qr_token);
    }

    public function edit(Branch $branch)
    {
        $this->authorizeBranch($branch);

        return view('owner.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $this->authorizeBranch($branch);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'address' => 'nullable|string|max:255',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'commission_percent' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update($validated);

        return redirect()->route('owner.branches.index')
            ->with('success', 'Cawangan "' . $branch->name . '" dikemaskini.');
    }

    public function destroy(Branch $branch)
    {
        $this->authorizeBranch($branch);

        if ($branch->barbers()->exists() || $branch->services()->exists()) {
            return back()->with('error', 'Tak boleh padam cawangan yang masih ada tukang gunting/servis. Nyahaktifkan cawangan (edit status) sebagai alternatif.');
        }

        $branch->delete();

        return redirect()->route('owner.branches.index')->with('success', 'Cawangan dipadam.');
    }

    /**
     * Papar halaman QR code besar untuk dicetak & digantung di kaunter cawangan.
     * Route: GET /owner/branches/{branch}/qr
     */
    public function qrPrint(Branch $branch)
    {
        $this->authorizeBranch($branch);

        $qrUrl = url('/q/' . $branch->qr_token);

        return view('owner.branches.qr-print', compact('branch', 'qrUrl'));
    }

    private function authorizeBranch(Branch $branch): void
    {
        abort_unless($branch->owner_id === Auth::user()->owner->id, 403);
    }
}
