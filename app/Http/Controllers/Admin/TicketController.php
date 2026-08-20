<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\QueueTicket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Senarai semua no. tiket (termasuk yang di-skip/cancelled), tapis ikut cawangan.
     * Route: GET /admin/tickets
     */
    public function index(Request $request)
    {
        $branches = Branch::with('owner')->orderBy('name')->get();
        $branchId = $request->input('branch_id');
        $status = $request->input('status');

        $tickets = QueueTicket::with(['branch.owner', 'service', 'barber', 'queueGroup'])
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return view('admin.tickets.index', compact('tickets', 'branches', 'branchId', 'status'));
    }
}
