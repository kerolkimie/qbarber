<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerServiceController extends Controller
{
    public function index(Branch $branch)
    {
        $this->authorizeBranch($branch);

        $services = $branch->services()->latest()->get();

        return view('owner.services.index', compact('branch', 'services'));
    }

    public function create(Branch $branch)
    {
        $this->authorizeBranch($branch);

        return view('owner.services.create', compact('branch'));
    }

    public function store(Request $request, Branch $branch)
    {
        $this->authorizeBranch($branch);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0|max:9999',
            'duration_minutes' => 'required|integer|min:5|max:480',
        ]);

        $service = Service::create([
            'branch_id' => $branch->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration_minutes' => $validated['duration_minutes'],
            'status' => 'active',
        ]);

        return redirect()->route('owner.branches.services.index', $branch)
            ->with('success', 'Servis "' . $service->name . '" berjaya ditambah.');
    }

    public function edit(Service $service)
    {
        $this->authorizeService($service);

        return view('owner.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorizeService($service);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0|max:9999',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'status' => 'required|in:active,inactive',
        ]);

        $service->update($validated);

        return redirect()->route('owner.branches.services.index', $service->branch_id)
            ->with('success', 'Servis "' . $service->name . '" dikemaskini.');
    }

    /**
     * Nyahaktifkan sahaja (bukan padam terus) supaya sejarah queue_tickets lama
     * yang guna servis ni kekal utuh untuk report.
     */
    public function destroy(Service $service)
    {
        $this->authorizeService($service);

        $service->update(['status' => 'inactive']);

        return back()->with('success', 'Servis "' . $service->name . '" dinyahaktifkan.');
    }

    private function authorizeBranch(Branch $branch): void
    {
        abort_unless($branch->owner_id === Auth::user()->owner->id, 403);
    }

    private function authorizeService(Service $service): void
    {
        abort_unless($service->branch->owner_id === Auth::user()->owner->id, 403);
    }
}
