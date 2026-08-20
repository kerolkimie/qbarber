<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Branch;
use App\Models\QueueCounter;
use App\Models\QueueGroup;
use App\Models\QueueTicket;
use App\Models\Service;
use App\Rules\MalaysianPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class QueueController extends Controller
{
    /**
     * Papar borang awam bila pelanggan scan QR code cawangan.
     * Route: GET /q/{qrToken}
     */
    public function show(string $qrToken)
    {
        $branch = Branch::where('qr_token', $qrToken)
            ->where('status', 'active')
            ->firstOrFail();

        $services = Service::where('branch_id', $branch->id)
            ->where('status', 'active')
            ->get();

        $barbers = Barber::where('branch_id', $branch->id)
            ->where('status', 'active')
            ->get();

        // Sistem giliran cuma boleh diguna kalau owner ada subscription AKTIF
        // (bukan lagi dihadkan ikut baki point).
        $subscriptionValid = $branch->owner->hasValidSubscription();
        $maxPax = $subscriptionValid ? 6 : 0;

        return view('queue.form', compact('branch', 'services', 'barbers', 'subscriptionValid', 'maxPax'));
    }

    /**
     * Proses borang: pelanggan hantar pax + servis setiap orang, sistem jana queue_group + tickets.
     * Route: POST /q/{qrToken}
     */
    public function store(Request $request, string $qrToken)
    {
        $branch = Branch::where('qr_token', $qrToken)
            ->where('status', 'active')
            ->firstOrFail();

        $pax = (int) $request->input('pax', 1);

        // Sistem cuma boleh diguna kalau owner ada subscription AKTIF.
        $subscriptionValid = $branch->owner->hasValidSubscription();
        $maxPax = $subscriptionValid ? 6 : 0;

        if ($maxPax < 1) {
            return back()->with('error', 'Maaf, kedai ini buat masa ini tidak dapat menerima tempahan baru. Sila hubungi kedai terus.');
        }

        $validated = $request->validate([
            'pax' => "required|integer|min:1|max:{$maxPax}",
            'services' => 'required|array|size:' . $pax,
            'services.*' => 'required|exists:services,id',
            'barbers' => 'nullable|array|size:' . $pax,
            'barbers.*' => 'nullable|exists:barbers,id',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => ['nullable', 'string', new MalaysianPhone()],
        ]);

        if (empty($validated['customer_phone'])) {
            $validated['customer_phone'] = null;
        }

        $group = QueueGroup::create([
            'branch_id' => $branch->id,
            'pax' => $validated['pax'],
            'customer_name' => $validated['customer_name'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
        ]);

        $ticketIds = [];

        foreach ($validated['services'] as $index => $serviceId) {
            $service = Service::findOrFail($serviceId);
            $preferredBarberId = $validated['barbers'][$index] ?? null;

            $ticket = QueueTicket::create([
                'queue_group_id' => $group->id,
                'branch_id' => $branch->id,
                'service_id' => $service->id,
                'preferred_barber_id' => $preferredBarberId ?: null,
                'ticket_number' => QueueCounter::nextNumber($branch->id),
                'status' => 'waiting',
                'estimated_minutes' => $this->estimateWaitMinutes($branch->id, $service),
            ]);

            $ticketIds[] = $ticket->id;
        }

        // Tiket kekal 'waiting' tanpa barber_id — tukang gunting akan panggil secara
        // manual bila mereka ready (elak tiket "terperangkap" kalau barber tiba-tiba
        // offline lepas auto-assign).

        // Simpan ID tiket dalam cookie (kekal 24 jam) supaya sistem boleh highlight
        // tiket pelanggan ni dalam skrin paparan nanti.
        Cookie::queue(Cookie::make('my_queue_tickets', implode(',', $ticketIds), 60 * 24));

        // Redirect ke GET page (bukan return view terus) - elak amaran "resubmit form"
        // bila pelanggan refresh, dan bagi page tiket auto-refresh dengan anggaran masa terkini.
        return redirect()->route('queue.group.show', $group);
    }

    /**
     * Papar semula page tiket (GET) - anggaran masa dikira SEMULA setiap kali dibuka
     * supaya sentiasa terkini (bukan nilai statik beku dari masa tiket dicipta).
     * Route: GET /ticket-group/{group}
     */
    public function showGroup(QueueGroup $group)
    {
        $branch = $group->branch;

        $liveTickets = $this->liveWaitingTickets($branch->id);

        $tickets = QueueTicket::with('service')
            ->where('queue_group_id', $group->id)
            ->get()
            ->map(function ($ticket) use ($liveTickets) {
                $live = $liveTickets->firstWhere('id', $ticket->id);
                $ticket->live_estimate = $live->live_estimate ?? 0;

                return $ticket;
            });

        return view('queue.ticket', compact('branch', 'group', 'tickets'));
    }

    /**
     * Anggaran awal (dikira sekali sahaja semasa tiket dicipta, disimpan dalam DB
     * sebagai rujukan). Paparan sebenar guna live_estimate yang dikira semula setiap refresh.
     */
    private function estimateWaitMinutes(int $branchId, Service $service): int
    {
        $waitingAhead = QueueTicket::where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->count();

        $activeBarbers = max(1, Barber::where('branch_id', $branchId)
            ->where('status', 'active')
            ->count());

        $avgDuration = $service->duration_minutes;

        return (int) ceil(($waitingAhead / $activeBarbers) * $avgDuration) + $avgDuration;
    }

    /**
     * Kira anggaran masa SECARA LANGSUNG (live) untuk semua tiket 'waiting' di cawangan,
     * ikut kedudukan sebenar dalam giliran SEKARANG (bukan nilai lama yang beku).
     * Dipanggil setiap kali skrin paparan / page tiket dibuka (setiap 10-15 saat via auto-refresh).
     */
    private function liveWaitingTickets(int $branchId)
    {
        $waiting = QueueTicket::with('service')
            ->where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->get();

        $activeBarbers = max(1, Barber::where('branch_id', $branchId)
            ->where('status', 'active')
            ->count());

        $runningMinutes = 0;

        return $waiting->map(function ($ticket) use (&$runningMinutes, $activeBarbers) {
            $duration = $ticket->service->duration_minutes;
            $ticket->live_estimate = (int) ceil($runningMinutes / $activeBarbers) + $duration;
            $runningMinutes += $duration;

            return $ticket;
        });
    }

    /**
     * Skrin paparan TV kedai — no semasa + senarai menunggu. Auto-refresh guna JS polling.
     * Guna qr_token (bukan ID nombor) supaya URL tak mudah diteka/ditukar sesuka hati.
     * Route: GET /branch/{qrToken}/display
     */
    public function display(string $qrToken)
    {
        $branch = Branch::where('qr_token', $qrToken)
            ->where('status', 'active')
            ->firstOrFail();

        $serving = QueueTicket::with(['service', 'barber'])
            ->where('branch_id', $branch->id)
            ->where('status', 'in_progress')
            ->get();

        $waiting = $this->liveWaitingTickets($branch->id)->take(10);

        // Baca cookie "my_queue_tickets" supaya sistem boleh highlight tiket sendiri.
        $myTicketIds = collect(explode(',', request()->cookie('my_queue_tickets', '')))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        $myTickets = QueueTicket::with('service')
            ->whereIn('id', $myTicketIds)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->get();

        return view('queue.display', compact('branch', 'serving', 'waiting', 'myTicketIds', 'myTickets'));
    }
}
