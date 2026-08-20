<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Rules\MalaysianPhone;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OwnerBarberController extends Controller
{
    public function index(Branch $branch)
    {
        $this->authorizeBranch($branch);

        $barbers = $branch->barbers()->with('user')->latest()->get();

        return view('owner.barbers.index', compact('branch', 'barbers'));
    }

    public function create(Branch $branch)
    {
        $this->authorizeBranch($branch);

        $owner = Auth::user()->owner;

        if (! $owner->canAddBarberToBranch($branch)) {
            return redirect()->route('owner.branches.barbers.index', $branch)
                ->with('error', $this->chairLimitMessage($owner));
        }

        return view('owner.barbers.create', compact('branch'));
    }

    /**
     * Daftar tukang gunting BARU — cipta akaun User (role=barber) sekali gus
     * supaya dia boleh terus log masuk ke /barber/dashboard.
     */
    public function store(Request $request, Branch $branch)
    {
        $this->authorizeBranch($branch);

        $owner = Auth::user()->owner;

        if (! $owner->canAddBarberToBranch($branch)) {
            return redirect()->route('owner.branches.barbers.index', $branch)
                ->with('error', $this->chairLimitMessage($owner));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email:rfc,filter|max:255|unique:users,email',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'password' => ['required', 'confirmed', new StrongPassword()],
            'payment_type' => 'required|in:commission,chair_rental',
            'rental_amount' => 'required_if:payment_type,chair_rental|nullable|numeric|min:0|max:99999',
            'rental_period' => 'required_if:payment_type,chair_rental|nullable|in:daily,weekly,monthly',
        ]);

        $barberRole = Role::firstOrCreate(['name' => 'barber']);

        $user = User::create([
            'role_id' => $barberRole->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'status' => 'active',
            // Akaun dicipta terus oleh owner (bukan daftar sendiri), jadi tak perlu verify emel.
            'email_verified_at' => now(),
        ]);

        Barber::create([
            'branch_id' => $branch->id,
            'user_id' => $user->id,
            'name' => $validated['name'],
            'status' => 'active',
            'current_state' => 'offline',
            'payment_type' => $validated['payment_type'],
            'rental_amount' => $validated['payment_type'] === 'chair_rental' ? $validated['rental_amount'] : null,
            'rental_period' => $validated['payment_type'] === 'chair_rental' ? $validated['rental_period'] : null,
        ]);

        return redirect()->route('owner.branches.barbers.index', $branch)
            ->with('success', 'Tukang gunting "' . $validated['name'] . '" berjaya didaftarkan. Emel login: ' . $validated['email']);
    }

    public function edit(Barber $barber)
    {
        $this->authorizeBarber($barber);

        $branches = Auth::user()->owner->branches;

        return view('owner.barbers.edit', compact('barber', 'branches'));
    }

    public function update(Request $request, Barber $barber)
    {
        $this->authorizeBarber($barber);

        $owner = Auth::user()->owner;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => ['nullable', 'string', new MalaysianPhone()],
            'status' => 'required|in:active,inactive',
            'branch_id' => 'required|exists:branches,id',
            'payment_type' => 'required|in:commission,chair_rental',
            'rental_amount' => 'required_if:payment_type,chair_rental|nullable|numeric|min:0|max:99999',
            'rental_period' => 'required_if:payment_type,chair_rental|nullable|in:daily,weekly,monthly',
        ]);

        $targetBranch = Branch::findOrFail($validated['branch_id']);
        $this->authorizeBranch($targetBranch); // pastikan cawangan destinasi milik owner sama

        $isMovingBranch = (int) $validated['branch_id'] !== (int) $barber->branch_id;

        if ($isMovingBranch) {
            // Semak had kerusi di cawangan DESTINASI (bukan cawangan asal barber ni).
            // Kira tanpa kira barber ni sendiri (dia belum "ada" di cawangan destinasi lagi).
            if (! $owner->canAddBarberToBranch($targetBranch)) {
                return back()->withInput()->with('error',
                    'Cawangan "' . $targetBranch->name . '" dah penuh (' . $this->chairLimitMessage($owner) . ')'
                );
            }
        }

        $barber->update([
            'name' => $validated['name'],
            'status' => $validated['status'],
            'branch_id' => $validated['branch_id'],
            'payment_type' => $validated['payment_type'],
            'rental_amount' => $validated['payment_type'] === 'chair_rental' ? $validated['rental_amount'] : null,
            'rental_period' => $validated['payment_type'] === 'chair_rental' ? $validated['rental_period'] : null,
        ]);

        $barber->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($isMovingBranch) {
            // Lepaskan tiket yang tergendala di cawangan LAMA (tiket cawangan lain
            // dah tak relevan untuk barber ni lepas pindah).
            \App\Models\QueueTicket::where('barber_id', $barber->id)
                ->whereIn('status', ['waiting', 'in_progress'])
                ->update([
                    'barber_id' => null,
                    'preferred_barber_id' => null,
                    'status' => 'waiting',
                    'started_at' => null,
                    'called_at' => null,
                ]);

            $barber->update(['current_state' => 'offline']);

            \App\Models\ActivityLog::record(
                'barber_transferred',
                "Owner pindahkan tukang gunting \"{$barber->name}\" ke cawangan \"{$targetBranch->name}\"",
                $barber
            );

            return redirect()->route('owner.branches.barbers.index', $targetBranch)
                ->with('success', 'Tukang gunting "' . $barber->name . '" berjaya dipindahkan ke ' . $targetBranch->name . '.');
        }

        return redirect()->route('owner.branches.barbers.index', $barber->branch_id)
            ->with('success', 'Maklumat tukang gunting dikemaskini.');
    }

    /**
     * "Padam" = nyahaktifkan sahaja (BUKAN delete rekod terus), supaya sejarah
     * queue_tickets yang pernah dia layan kekal utuh untuk report.
     */
    public function destroy(Barber $barber)
    {
        $this->authorizeBarber($barber);

        // Lepaskan sebarang tiket yang tergendala pada barber ni (assigned/in_progress)
        // supaya tukang gunting lain boleh ambil alih.
        \App\Models\QueueTicket::where('barber_id', $barber->id)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->update([
                'barber_id' => null,
                'preferred_barber_id' => null,
                'status' => 'waiting',
                'started_at' => null,
                'called_at' => null,
            ]);

        // Lepaskan juga tiket yang belum dipanggil tapi khusus pilih barber ni.
        \App\Models\QueueTicket::where('preferred_barber_id', $barber->id)
            ->where('status', 'waiting')
            ->whereNull('barber_id')
            ->update(['preferred_barber_id' => null]);

        $barber->update(['status' => 'inactive', 'current_state' => 'offline']);
        $barber->user->update(['status' => 'suspended']);

        return back()->with('success', 'Tukang gunting "' . $barber->name . '" dinyahaktifkan.');
    }

    private function chairLimitMessage($owner): string
    {
        $limit = $owner->chairLimit();

        if ($limit <= 0) {
            return 'Anda perlu ada subscription aktif sebelum boleh daftar tukang gunting. Sila pilih pakej dahulu.';
        }

        $scope = $owner->isPerBranchChairLimit() ? 'setiap cawangan' : 'jumlah keseluruhan cawangan anda';

        return "Pakej semasa anda hanya benarkan {$limit} kerusi (tukang gunting) untuk {$scope}. Sila upgrade pakej untuk tambah lagi.";
    }

    private function authorizeBranch(Branch $branch): void
    {
        abort_unless($branch->owner_id === Auth::user()->owner->id, 403);
    }

    private function authorizeBarber(Barber $barber): void
    {
        abort_unless($barber->branch->owner_id === Auth::user()->owner->id, 403);
    }
}
