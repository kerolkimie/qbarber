<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Branch;
use App\Models\QueueTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerReportController extends Controller
{
    /**
     * Laporan pendapatan ikut cawangan — boleh tapis hari/minggu/bulan, dan
     * tapis ke satu cawangan spesifik untuk lihat pecahan ikut tukang gunting.
     * Route: GET /owner/report
     */
    public function index(Request $request)
    {
        $owner = Auth::user()->owner;
        $branches = $owner->branches;

        $period = in_array($request->input('period'), ['day', 'week', 'month']) ? $request->input('period') : 'month';
        $selectedBranchId = $request->input('branch_id');

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

        $branchesToReport = $selectedBranchId
            ? $branches->where('id', $selectedBranchId)
            : $branches;

        $report = $branchesToReport->map(function ($branch) use ($from, $to) {
            $tickets = QueueTicket::with(['service', 'barber'])
                ->where('branch_id', $branch->id)
                ->where('status', 'completed')
                ->whereBetween('completed_at', [$from, $to])
                ->get();

            $revenue = $tickets->sum(fn ($t) => $t->service->price);

            // Komisen kedai kena bayar HANYA untuk tiket yang dilayan barber
            // model 'commission' — barber 'chair_rental' tak kena potong komisen
            // (dia dah bayar sewa berasingan, tak dikira ikut tempoh laporan ni).
            $commissionRevenue = $tickets
                ->filter(fn ($t) => $t->barber && ! $t->barber->isChairRental())
                ->sum(fn ($t) => $t->service->price);

            $commission = $commissionRevenue * ($branch->commission_percent / 100);

            $rentalBarberCount = $branch->barbers()->where('payment_type', 'chair_rental')->count();

            return (object) [
                'branch' => $branch,
                'customers' => $tickets->count(),
                'revenue' => $revenue,
                'commission' => $commission,
                'net' => $revenue - $commission,
                'rental_barber_count' => $rentalBarberCount,
            ];
        })->values();

        $totals = (object) [
            'customers' => $report->sum('customers'),
            'revenue' => $report->sum('revenue'),
            'commission' => $report->sum('commission'),
            'net' => $report->sum('net'),
        ];

        // Kalau owner tapis ke SATU cawangan, papar pecahan ikut tukang gunting sekali.
        $barberBreakdown = null;

        if ($selectedBranchId) {
            $branch = $branches->firstWhere('id', (int) $selectedBranchId);

            if ($branch) {
                $barberBreakdown = Barber::where('branch_id', $branch->id)
                    ->get()
                    ->map(function ($barber) use ($from, $to, $branch) {
                        $tickets = QueueTicket::with('service')
                            ->where('barber_id', $barber->id)
                            ->where('status', 'completed')
                            ->whereBetween('completed_at', [$from, $to])
                            ->get();

                        $revenue = $tickets->sum(fn ($t) => $t->service->price);

                        return (object) [
                            'barber' => $barber,
                            'customers' => $tickets->count(),
                            'revenue' => $revenue,
                            'commission' => $barber->isChairRental() ? null : $revenue * ($branch->commission_percent / 100),
                            'is_rental' => $barber->isChairRental(),
                        ];
                    })
                    ->sortByDesc('revenue')
                    ->values();
            }
        }

        return view('owner.report', compact(
            'branches', 'report', 'totals', 'period', 'label', 'inputValue',
            'selectedBranchId', 'prevAnchor', 'nextAnchor', 'barberBreakdown', 'from', 'to'
        ));
    }
}
