<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;

class LandingController extends Controller
{
    /**
     * Landing page — termasuk sekali senarai pakej harga (menu "Harga" scroll ke sini).
     * Route: GET /
     */
    public function index()
    {
        $plans = SubscriptionPlan::where('status', 'active')
            ->orderBy('price')
            ->get();

        // Sementara admin belum tambah pelan sebenar melalui portal admin,
        // papar contoh pakej supaya halaman ni tak kosong semasa development.
        if ($plans->isEmpty()) {
            $plans = collect([
                (object) [
                    'name' => 'Basic',
                    'price' => 70,
                    'duration_days' => 30,
                    'max_branches' => 1,
                    'max_barbers' => 5,
                    'is_per_branch_limit' => true,
                    'features' => "Sesuai untuk kedai tunggal\nQR code tanpa had\nSkrin paparan giliran masa nyata\nSokongan emel",
                ],
                (object) [
                    'name' => 'Pro',
                    'price' => 120,
                    'duration_days' => 30,
                    'max_branches' => 2,
                    'max_barbers' => 5,
                    'is_per_branch_limit' => true,
                    'features' => "Sesuai untuk 2 lokasi kecil\nSemua ciri Basic\nLaporan & analitik lanjutan\nSokongan keutamaan",
                ],
                (object) [
                    'name' => 'Premium',
                    'price' => 180,
                    'duration_days' => 30,
                    'max_branches' => 3,
                    'max_barbers' => 5,
                    'is_per_branch_limit' => true,
                    'features' => "Untuk rangkaian barbershop berkembang\nSemua ciri Pro\nAkses API\nAkaun pengurus khusus",
                ],
            ]);
        }

        return view('landing', compact('plans'));
    }
}
