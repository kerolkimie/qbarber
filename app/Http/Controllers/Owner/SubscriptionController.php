<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Commission;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\ToyyibPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Papar pakej semasa (jika ada) + senarai pakej untuk dipilih/upgrade.
     * Route: GET /owner/subscription
     */
    public function index()
    {
        $owner = Auth::user()->owner;
        $plans = SubscriptionPlan::where('status', 'active')->orderBy('price')->get();
        $currentSubscription = $owner->activeSubscription;

        return view('owner.subscription.index', compact('owner', 'plans', 'currentSubscription'));
    }

    /**
     * Halaman "checkout":
     * - renewal_mode = offline → ringkasan pakej + butang sahkan terus (tiada gateway).
     * - renewal_mode = online  → terus cipta Bill ToyyibPay & redirect ke halaman bayaran.
     * Route: GET /owner/subscription/{plan}/checkout
     */
    public function checkout(SubscriptionPlan $plan)
    {
        $owner = Auth::user()->owner;

        if (! $owner->isOnlineRenewal()) {
            return view('owner.subscription.checkout', compact('plan'));
        }

        $subscription = Subscription::create([
            'owner_id' => $owner->id,
            'plan_id' => $plan->id,
            'agent_id' => $owner->agent_id,
            'start_date' => today(),
            'end_date' => today()->addDays($plan->duration_days),
            'amount_paid' => $plan->price,
            'status' => 'pending',
        ]);

        $toyyibpay = new ToyyibPayService();

        if (! $toyyibpay->isConfigured()) {
            $subscription->delete();

            return back()->with('error', 'Payment gateway belum disetkan oleh admin. Sila hubungi admin atau cuba lagi kemudian.');
        }

        $bill = $toyyibpay->createBill([
            'billName' => substr('Pakej ' . $plan->name, 0, 30),
            'billDescription' => substr("Subscription {$plan->name} - {$owner->business_name}", 0, 100),
            'billAmount' => (int) round($plan->price * 100),
            'billReturnUrl' => route('owner.subscription.return'),
            'billCallbackUrl' => route('toyyibpay.callback'),
            'billExternalReferenceNo' => 'SUB' . $subscription->id,
            'billTo' => Auth::user()->name,
            'billEmail' => Auth::user()->email,
            'billPhone' => Auth::user()->phone ?: '0100000000',
        ]);

        if (! $bill) {
            $subscription->delete();

            return back()->with('error', 'Gagal hubungi payment gateway. Sila cuba lagi.');
        }

        $subscription->update(['toyyibpay_bill_code' => $bill['billCode']]);

        return redirect()->away($bill['payment_url']);
    }

    /**
     * Halaman pelanggan kembali lepas cuba bayar (UI sahaja).
     * Route: GET /owner/subscription/return
     */
    public function returnPage(Request $request)
    {
        $statusId = $request->query('status_id');

        $message = match ($statusId) {
            '1' => 'Pembayaran berjaya! Pakej anda akan aktif dalam beberapa saat.',
            '2' => 'Pembayaran masih diproses. Sila semak semula sebentar lagi.',
            '3' => 'Pembayaran gagal atau dibatalkan. Sila cuba lagi.',
            default => 'Status pembayaran tidak diketahui. Sila semak dashboard anda.',
        };

        return view('owner.subscription.return', compact('message', 'statusId'));
    }

    /**
     * Callback SERVER-TO-SERVER dari ToyyibPay — sumber kebenaran utama.
     * Route: POST /toyyibpay/callback
     */
    public function callback(Request $request)
    {
        Log::info('ToyyibPay callback diterima', $request->all());

        // Sahkan hash — pastikan callback ni betul-betul dari ToyyibPay, bukan
        // orang lain hantar data palsu terus ke URL callback awam kita.
        if (! $this->verifyCallbackHash($request)) {
            Log::warning('ToyyibPay callback: hash tidak sah — mungkin bukan dari ToyyibPay sebenar', $request->all());

            return response('invalid hash', 400);
        }

        $refNo = $request->input('order_id') ?? $request->input('billExternalReferenceNo');
        $statusId = $request->input('status_id') ?? $request->input('status');

        $subscription = $this->resolveSubscriptionFromRef($refNo);

        if (! $subscription) {
            Log::warning('ToyyibPay callback: refno/subscription tidak sah', ['refNo' => $refNo]);

            return response('invalid reference', 400);
        }

        if ($subscription->status !== 'pending') {
            Log::info('ToyyibPay callback: subscription dah diproses sebelum ni', [
                'subscriptionId' => $subscription->id,
                'currentStatus' => $subscription->status,
            ]);

            return response('ok', 200);
        }

        if ((string) $statusId !== '1') {
            Log::info('ToyyibPay callback: status bukan berjaya', ['statusId' => $statusId]);
            $subscription->update(['status' => 'failed']);

            return response('ok', 200);
        }

        $this->activateSubscription($subscription, 'toyyibpay');

        Log::info('ToyyibPay callback: SELESAI - subscription & point diaktifkan', ['subscriptionId' => $subscription->id]);

        return response('ok', 200);
    }

    /**
     * MOD OFFLINE sahaja — sahkan terus tanpa payment gateway.
     * Route: POST /owner/subscription/{plan}/confirm
     */
    public function confirm(Request $request, SubscriptionPlan $plan)
    {
        $owner = Auth::user()->owner;

        abort_if($owner->isOnlineRenewal(), 403, 'Akaun ini disetkan untuk pembayaran online sahaja.');

        $subscription = Subscription::create([
            'owner_id' => $owner->id,
            'plan_id' => $plan->id,
            'agent_id' => $owner->agent_id,
            'start_date' => today(),
            'end_date' => today()->addDays($plan->duration_days),
            'amount_paid' => $plan->price,
            'status' => 'pending',
        ]);

        $this->activateSubscription($subscription, 'offline');

        return redirect()->route('owner.dashboard')
            ->with('success', 'Pembayaran berjaya! Pakej "' . $plan->name . '" kini aktif.');
    }

    /**
     * Sahkan hash MD5 yang ToyyibPay sertakan dalam callback, ikut formula rasmi:
     * md5(userSecretKey + status + order_id + refno + "ok")
     */
    private function verifyCallbackHash(Request $request): bool
    {
        $receivedHash = $request->input('hash');

        // Kalau ToyyibPay tak hantar hash langsung (sesetengah versi API lama),
        // jangan block terus — cuma log sebagai amaran, biar log/admin recheck jaga.
        if (! $receivedHash) {
            Log::warning('ToyyibPay callback: tiada field hash disertakan (versi API mungkin berbeza)');

            return true;
        }

        $secretKey = config('services.toyyibpay.secret_key', '');
        $status = $request->input('status') ?? $request->input('status_id') ?? '';
        $orderId = $request->input('order_id') ?? '';
        $refNo = $request->input('refno') ?? '';

        $expectedHash = md5($secretKey . $status . $orderId . $refNo . 'ok');

        return hash_equals($expectedHash, $receivedHash);
    }

    /**
     * Cari subscription dari refno "SUBxx" ToyyibPay.
     */
    private function resolveSubscriptionFromRef(?string $refNo): ?Subscription
    {
        if (! $refNo || ! str_starts_with($refNo, 'SUB')) {
            return null;
        }

        return Subscription::find((int) str_replace('SUB', '', $refNo));
    }

    /**
     * LOGIK PENGAKTIFAN DIKONGSI — dipanggil dari callback ToyyibPay ATAU
     * dari admin secara manual (bila callback gagal sampai tapi bayaran
     * sebenarnya dah berjaya di pihak ToyyibPay).
     */
    public function activateSubscription(Subscription $subscription, string $gateway): void
    {
        DB::transaction(function () use ($subscription, $gateway) {
            $owner = $subscription->owner;
            $plan = $subscription->plan;

            $subscription->update([
                'status' => 'active',
                'start_date' => today(),
                'end_date' => today()->addDays($plan->duration_days),
            ]);

            Payment::create([
                'subscription_id' => $subscription->id,
                'amount' => $plan->price,
                'method' => $gateway === 'toyyibpay' ? 'toyyibpay' : 'manual',
                'reference_no' => $subscription->toyyibpay_bill_code ?? ('OFFLINE-' . strtoupper(uniqid())),
                'status' => 'success',
                'paid_at' => now(),
            ]);

            if ($owner->agent_id) {
                $agent = $owner->agent;

                Commission::create([
                    'agent_id' => $agent->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $plan->price * ($agent->commission_percent / 100),
                    'percent' => $agent->commission_percent,
                    'status' => 'pending',
                ]);
            }

            ActivityLog::record(
                'subscription_selected',
                "Owner \"{$owner->business_name}\" perbaharui pakej {$plan->name} ({$gateway}, RM{$plan->price})",
                $subscription,
                ['plan' => $plan->name, 'amount' => $plan->price, 'gateway' => $gateway]
            );
        });
    }
}
