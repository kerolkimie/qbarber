<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['owner', 'plan', 'agent.user', 'payments'])
            ->latest()
            ->get();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Semak status SEBENAR bill ni terus dari ToyyibPay (bukan setakat rekod
     * dalam DB kita) — untuk kes callback tak sampai tapi bayaran dah berjaya.
     * Kalau sah berjaya, terus aktifkan (guna logik sama macam callback biasa).
     * Route: POST /admin/subscriptions/{subscription}/recheck
     */
    public function recheck(\App\Http\Controllers\Owner\SubscriptionController $ownerSubscriptionController, Subscription $subscription)
    {
        if ($subscription->status === 'active') {
            return back()->with('success', 'Subscription ni dah aktif pun.');
        }

        if (! $subscription->toyyibpay_bill_code) {
            return back()->with('error', 'Subscription ni tiada bill code ToyyibPay (mungkin dibuat secara offline).');
        }

        $toyyibpay = new \App\Services\ToyyibPayService();
        $statusId = $toyyibpay->checkBillStatus($subscription->toyyibpay_bill_code);

        if ($statusId === null) {
            return back()->with('error', 'Gagal hubungi ToyyibPay untuk semak status. Cuba lagi sebentar.');
        }

        if ($statusId !== 1) {
            $label = match ($statusId) { 2 => 'masih pending', 3 => 'gagal', default => 'tidak diketahui (' . $statusId . ')' };

            return back()->with('error', "Status di ToyyibPay: {$label}. Belum boleh diaktifkan.");
        }

        $ownerSubscriptionController->activateSubscription($subscription, 'toyyibpay');

        return back()->with('success', 'Disahkan berjaya di ToyyibPay — subscription & point berjaya diaktifkan!');
    }
}
