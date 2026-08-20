<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\PointBatch;
use Illuminate\Http\Request;

class TopupController extends Controller
{
    /**
     * Senarai SEMUA transaksi topup (bakal panjang) — jadual dinamik + pagination.
     * Route: GET /admin/topups
     */
    public function index(Request $request)
    {
        $owners = Owner::orderBy('business_name')->get();
        $ownerId = $request->input('owner_id');

        $topups = PointBatch::with(['owner', 'topupPackage'])
            ->where('source', 'topup')
            ->when($ownerId, fn ($q) => $q->where('owner_id', $ownerId))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $totalRevenue = PointBatch::where('source', 'topup')->sum('price_paid');

        return view('admin.topups.index', compact('topups', 'owners', 'ownerId', 'totalRevenue'));
    }
}
