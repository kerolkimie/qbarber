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
     * Senarai tiket DILAYAN HARI INI, boleh tapis ikut cawangan.
     * Route: GET /owner/tickets/served
     */
    public function served(Request $request)
    {
        $branches = $this->branches();
        $branchId = $request->input('branch_id');

        $tickets = QueueTicket::with(['branch', 'service', 'barber'])
            ->whereIn('branch_id', $this->ownerBranchIds())
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->orderByDesc('completed_at')
            ->paginate(30)
            ->withQueryString();

        return view('owner.tickets.served', compact('tickets', 'branches', 'branchId'));
    }

    /**
     * Senarai tiket MENUNGGU / SEDANG DILAYAN (belum siap), boleh tapis ikut cawangan.
     * Route: GET /owner/tickets/waiting
     */
    public function waiting(Request $request)
    {
        $branches = $this->branches();
        $branchId = $request->input('branch_id');

        $tickets = QueueTicket::with(['branch', 'service', 'barber', 'preferredBarber'])
            ->whereIn('branch_id', $this->ownerBranchIds())
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->whereIn('status', ['waiting', 'in_progress'])
            ->orderBy('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('owner.tickets.waiting', compact('tickets', 'branches', 'branchId'));
    }

    /**
     * Sejarah PENGGUNAAN POINT (semua tiket completed, 1 tiket = 1 point) —
     * boleh tapis ikut cawangan & julat tarikh (utk pautan drill-down dari laporan).
     * Route: GET /owner/tickets/history
     */
    public function history(Request $request)
    {
        $branches = $this->branches();
        $branchId = $request->input('branch_id');
        $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : null;

        $tickets = QueueTicket::with(['branch', 'service', 'barber'])
            ->whereIn('branch_id', $this->ownerBranchIds())
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->where('status', 'completed')
            ->when($from, fn ($q) => $q->where('completed_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('completed_at', '<=', $to))
            ->orderByDesc('completed_at')
            ->paginate(30)
            ->withQueryString();

        return view('owner.tickets.history', compact('tickets', 'branches', 'branchId', 'from', 'to'));
    }
}
