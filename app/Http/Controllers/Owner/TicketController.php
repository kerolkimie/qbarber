<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\QueueTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    private function ownerBranchIds()
    {
        return Auth::user()->owner->branches()->pluck('id');
    }

    private function branches()
    {
        return Auth::user()->owner->branches;
    }

    /**
     * Carian ringkas — padan no. tiket, nama servis, atau nama tukang gunting.
     */
    private function applySearch($query, ?string $q)
    {
        if (! $q) {
            return $query;
        }

        return $query->where(function ($sub) use ($q) {
            $sub->where('ticket_number', 'like', "%{$q}%")
                ->orWhereHas('service', fn ($s) => $s->where('name', 'like', "%{$q}%"))
                ->orWhereHas('barber', fn ($b) => $b->where('name', 'like', "%{$q}%"))
                ->orWhereHas('preferredBarber', fn ($b) => $b->where('name', 'like', "%{$q}%"));
        });
    }

    /**
     * Senarai tiket DILAYAN HARI INI, boleh tapis ikut cawangan & carian.
     * Route: GET /owner/tickets/served
     */
    public function served(Request $request)
    {
        $branches = $this->branches();
        $branchId = $request->input('branch_id');
        $q = $request->input('q');

        $tickets = $this->applySearch(
            QueueTicket::with(['branch', 'service', 'barber'])
                ->whereIn('branch_id', $this->ownerBranchIds())
                ->when($branchId, fn ($qr) => $qr->where('branch_id', $branchId))
                ->where('status', 'completed')
                ->whereDate('completed_at', today()),
            $q
        )
            ->orderByDesc('completed_at')
            ->paginate(30)
            ->withQueryString();

        return view('owner.tickets.served', compact('tickets', 'branches', 'branchId', 'q'));
    }

    /**
     * Senarai tiket MENUNGGU / SEDANG DILAYAN, boleh tapis ikut cawangan & carian.
     * Route: GET /owner/tickets/waiting
     */
    public function waiting(Request $request)
    {
        $branches = $this->branches();
        $branchId = $request->input('branch_id');
        $q = $request->input('q');

        $tickets = $this->applySearch(
            QueueTicket::with(['branch', 'service', 'barber', 'preferredBarber'])
                ->whereIn('branch_id', $this->ownerBranchIds())
                ->when($branchId, fn ($qr) => $qr->where('branch_id', $branchId))
                ->whereIn('status', ['waiting', 'in_progress']),
            $q
        )
            ->orderBy('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('owner.tickets.waiting', compact('tickets', 'branches', 'branchId', 'q'));
    }

    /**
     * Sejarah tiket (drill-down laporan), boleh tapis ikut cawangan, tarikh & carian.
     * Route: GET /owner/tickets/history
     */
    public function history(Request $request)
    {
        $branches = $this->branches();
        $branchId = $request->input('branch_id');
        $q = $request->input('q');
        $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : null;

        $tickets = $this->applySearch(
            QueueTicket::with(['branch', 'service', 'barber'])
                ->whereIn('branch_id', $this->ownerBranchIds())
                ->when($branchId, fn ($qr) => $qr->where('branch_id', $branchId))
                ->where('status', 'completed')
                ->when($from, fn ($qr) => $qr->where('completed_at', '>=', $from))
                ->when($to, fn ($qr) => $qr->where('completed_at', '<=', $to)),
            $q
        )
            ->orderByDesc('completed_at')
            ->paginate(30)
            ->withQueryString();

        return view('owner.tickets.history', compact('tickets', 'branches', 'branchId', 'from', 'to', 'q'));
    }
}
