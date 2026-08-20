<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\BarberShift;
use App\Models\QueueTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarberController extends Controller
{
    /**
     * Dashboard tukang gunting: tiket semasa, statistik hari/minggu/bulan, status shift.
     * Route: GET /barber/dashboard
     */
    public function dashboard()
    {
        $barber = Auth::user()->barber;

        $currentTicket = QueueTicket::with(['service', 'queueGroup'])
            ->where('barber_id', $barber->id)
            ->where('status', 'in_progress')
            ->first();

        // Tiket yang baru dipanggil (barber tekan "Panggil Seterusnya") tapi belum Start.
        $assignedTicket = QueueTicket::with(['service', 'queueGroup'])
            ->where('barber_id', $barber->id)
            ->where('status', 'waiting')
            ->first();

        $waitingCount = QueueTicket::where('branch_id', $barber->branch_id)
            ->where('status', 'waiting')
            ->whereNull('barber_id')
            ->count();

        $stats = [
            'today' => $this->completedCount($barber->id, today(), today()->endOfDay()),
            'week' => $this->completedCount($barber->id, now()->startOfWeek(), now()->endOfWeek()),
            'month' => $this->completedCount($barber->id, now()->startOfMonth(), now()->endOfMonth()),
        ];

        $todayShift = $barber->todayShift();

        return view('barber.dashboard', compact(
            'barber', 'currentTicket', 'assignedTicket', 'waitingCount', 'stats', 'todayShift'
        ));
    }

    private function completedCount(int $barberId, $from, $to): int
    {
        return QueueTicket::where('barber_id', $barberId)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->count();
    }

    /**
     * Tukang gunting tekan MULA TUGASAN — buka shift, jadi 'available'.
     * TIADA auto-assign — barber kena tekan "Panggil Pelanggan Seterusnya" sendiri.
     * Route: POST /barber/shift/start
     */
    public function startShift()
    {
        $barber = Auth::user()->barber;

        abort_unless($barber->status === 'active', 403, 'Akaun tukang gunting ini telah dinyahaktifkan. Hubungi pemilik barbershop.');

        $shift = BarberShift::firstOrCreate(
            ['barber_id' => $barber->id, 'shift_date' => today()],
            []
        );

        $shift->update(['clock_in' => now(), 'clock_out' => null]);

        $barber->update(['current_state' => 'available']);

        return back()->with('success', 'Tugasan dimulakan. Tekan "Panggil Pelanggan Seterusnya" bila anda ready.');
    }

    /**
     * Tukang gunting tekan TAMAT TUGASAN.
     * Route: POST /barber/shift/end
     */
    public function endShift(Request $request)
    {
        $barber = Auth::user()->barber;

        $activeTickets = QueueTicket::where('barber_id', $barber->id)
            ->whereIn('status', ['waiting', 'in_progress'])
            ->get();

        if ($activeTickets->isNotEmpty() && ! $request->boolean('force')) {
            return back()->with('error', 'Anda masih ada tiket aktif. Kalau ada hal kecemasan, tekan "Tamat Tugasan Kecemasan" — tiket akan dikembalikan ke giliran untuk tukang gunting lain.');
        }

        foreach ($activeTickets as $ticket) {
            $ticket->update([
                'barber_id' => null,
                'preferred_barber_id' => null,
                'status' => 'waiting',
                'started_at' => null,
                'called_at' => null,
            ]);
        }

        // Lepaskan juga tiket yang BELUM dipanggil lagi tapi pelanggan khusus
        // pilih barber ni (preferred_barber_id) — kalau tak dilepaskan, tiket
        // ni terperangkap selama-lamanya sebab tiada barber lain boleh panggil
        // tiket yang "dikunci" untuk barber yang dah offline.
        $releasedCount = QueueTicket::where('preferred_barber_id', $barber->id)
            ->where('status', 'waiting')
            ->whereNull('barber_id')
            ->update(['preferred_barber_id' => null]);

        $shift = $barber->todayShift();

        if ($shift) {
            $shift->update(['clock_out' => now()]);
        }

        $barber->update(['current_state' => 'offline']);

        $totalReleased = $activeTickets->count() + $releasedCount;

        return back()->with('success', $totalReleased > 0
            ? "Tugasan ditamatkan. {$totalReleased} tiket yang belum selesai/dipanggil telah dikembalikan ke giliran untuk tukang gunting lain."
            : 'Tugasan hari ini ditamatkan. Terima kasih!');
    }

    /**
     * Tukang gunting tekan "PANGGIL PELANGGAN SETERUSNYA" — ambil tiket 'waiting'
     * paling awal secara MANUAL (bukan auto-assign sistem). Ini punca utama kawal
     * bila barber betul-betul ready, elak tiket "terperangkap" pada barber yang
     * tiba-tiba tak available.
     * Route: POST /barber/call-next
     */
    public function callNext()
    {
        $barber = Auth::user()->barber;

        abort_unless($barber->status === 'active', 403, 'Akaun tukang gunting ini telah dinyahaktifkan.');

        if ($barber->current_state !== 'available') {
            return back()->with('error', 'Anda perlu dalam status "available" untuk panggil pelanggan seterusnya.');
        }

        // Utamakan tiket yang pelanggan KHUSUS pilih barber ni, baru tiket
        // tiada pilihan (mana-mana barber pun boleh). Dalam kumpulan sama,
        // ikut siapa lama menunggu dulu.
        $ticket = QueueTicket::where('branch_id', $barber->branch_id)
            ->where('status', 'waiting')
            ->whereNull('barber_id')
            ->where(function ($q) use ($barber) {
                $q->where('preferred_barber_id', $barber->id)
                  ->orWhereNull('preferred_barber_id');
            })
            ->orderByRaw('preferred_barber_id IS NULL ASC') // pilihan khusus dulu
            ->orderBy('created_at')
            ->first();

        if (! $ticket) {
            return back()->with('error', 'Tiada pelanggan menunggu buat masa ini.');
        }

        $ticket->update([
            'barber_id' => $barber->id,
            'called_at' => now(),
        ]);

        $barber->update(['current_state' => 'busy']);

        return back()->with('success', "Tiket #{$ticket->ticket_number} dipanggil.");
    }

    /**
     * Tukang gunting tekan START — mulakan servis untuk tiket yang dipanggil.
     * Route: POST /barber/ticket/{ticket}/start
     */
    public function start(QueueTicket $ticket)
    {
        $barber = Auth::user()->barber;

        abort_unless($ticket->barber_id === $barber->id, 403);
        abort_unless($ticket->status === 'waiting', 422, 'Tiket bukan dalam status menunggu.');
        abort_unless($barber->status === 'active', 403, 'Akaun tukang gunting ini telah dinyahaktifkan.');

        $ticket->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $barber->update(['current_state' => 'busy']);

        return back()->with('success', "Tiket #{$ticket->ticket_number} dimulakan.");
    }

    /**
     * Tukang gunting tekan NEXT — tandakan siap, bebaskan barber (jadi 'available').
     * TIADA auto-assign — barber kena tekan "Panggil Pelanggan Seterusnya" untuk
     * pelanggan seterusnya, ikut kesediaan dia sendiri.
     * Route: POST /barber/ticket/{ticket}/next
     */
    public function next(QueueTicket $ticket)
    {
        $barber = Auth::user()->barber;

        abort_unless($ticket->barber_id === $barber->id, 403);

        DB::transaction(function () use ($ticket, $barber) {
            $ticket->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $barber->update([
                'current_state' => 'available',
                'last_completed_at' => now(),
            ]);
        });

        return back()->with('success', "Tiket #{$ticket->ticket_number} selesai. Tekan \"Panggil Pelanggan Seterusnya\" bila ready.");
    }

    /**
     * Tukang gunting tekan SKIP — pelanggan tak muncul / nak batal.
     * Route: POST /barber/ticket/{ticket}/skip
     */
    public function skip(QueueTicket $ticket)
    {
        $barber = Auth::user()->barber;

        abort_unless($ticket->barber_id === $barber->id, 403);
        abort_unless($ticket->status === 'waiting', 422, 'Tiket bukan dalam status menunggu.');

        DB::transaction(function () use ($ticket, $barber) {
            $ticket->update([
                'status' => 'cancelled',
                'barber_id' => null,
            ]);

            $barber->update(['current_state' => 'available']);
        });

        return back()->with('success', "Tiket #{$ticket->ticket_number} dilangkau (pelanggan tidak hadir).");
    }

    /**
     * Laporan pendapatan terperinci — tab Hari/Minggu/Bulan, setiap satu boleh
     * ditapis ke tarikh/minggu/bulan tertentu.
     * Route: GET /barber/earnings
     */
    public function earnings(Request $request)
    {
        $barber = Auth::user()->barber;
        $period = in_array($request->input('period'), ['day', 'week', 'month']) ? $request->input('period') : 'day';

        if ($period === 'day') {
            $anchor = $request->filled('date') ? \Carbon\Carbon::parse($request->input('date')) : today();
            $from = $anchor->copy()->startOfDay();
            $to = $anchor->copy()->endOfDay();
            $label = $anchor->translatedFormat('d M Y');
            $inputValue = $anchor->format('Y-m-d');
        } elseif ($period === 'week') {
            $anchor = $request->filled('date') ? \Carbon\Carbon::parse($request->input('date')) : today();
            $from = $anchor->copy()->startOfWeek();
            $to = $anchor->copy()->endOfWeek();
            $label = $from->translatedFormat('d M') . ' - ' . $to->translatedFormat('d M Y');
            $inputValue = $anchor->format('Y-m-d');
        } else {
            $anchor = $request->filled('month')
                ? \Carbon\Carbon::parse($request->input('month') . '-01')
                : today()->startOfMonth();
            $from = $anchor->copy()->startOfMonth();
            $to = $anchor->copy()->endOfMonth();
            $label = $anchor->translatedFormat('F Y');
            $inputValue = $anchor->format('Y-m');
        }

        $tickets = QueueTicket::with('service')
            ->where('barber_id', $barber->id)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->orderByDesc('completed_at')
            ->get();

        $totalRevenue = $tickets->sum(fn ($t) => $t->service->price);
        $commissionPercent = $barber->branch->commission_percent;

        if ($barber->isChairRental()) {
            // Sewa kerusi: barber simpan 100% hasil servis, tak ada potongan komisen.
            $totalIncome = $totalRevenue;
        } else {
            $totalIncome = $totalRevenue * ($commissionPercent / 100);
        }

        $totalCustomers = $tickets->count();

        $prevAnchor = match ($period) {
            'day' => $anchor->copy()->subDay()->format('Y-m-d'),
            'week' => $anchor->copy()->subWeek()->format('Y-m-d'),
            'month' => $anchor->copy()->subMonthNoOverflow()->format('Y-m'),
        };
        $nextAnchor = match ($period) {
            'day' => $anchor->copy()->addDay()->format('Y-m-d'),
            'week' => $anchor->copy()->addWeek()->format('Y-m-d'),
            'month' => $anchor->copy()->addMonthNoOverflow()->format('Y-m'),
        };

        return view('barber.earnings', compact(
            'barber', 'period', 'label', 'inputValue', 'tickets',
            'totalIncome', 'totalRevenue', 'commissionPercent', 'totalCustomers', 'prevAnchor', 'nextAnchor'
        ));
    }
}
