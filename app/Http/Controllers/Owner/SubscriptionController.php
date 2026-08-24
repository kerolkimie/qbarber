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
     * Papar pakej semasa + pakej BERJADUAL (kalau ada) + senarai pakej untuk dipilih.
     * Route: GET /owner/subscription
     */
    public function index()
    {
        $owner = Auth::user()->owner;
        $plans = SubscriptionPlan::where('status', 'active')->orderBy('price')->get();
        $currentSubscription = $owner->effectiveSubscription();
        $upcomingSubscription = $owner->upcomingSubscription();

        return view('owner.subscription.index', compact('owner', 'plans', 'currentSubscription', 'upcomingSubscription'));
    }

    /**
     * Halaman "checkout". Kalau owner dah ada pakej yang masih berkuat kuasa/
     * berjadual, pakej BARU ni akan DIJADUALKAN bermula sehari lepas pakej
     * sedia ada tamat (bukan tukar terus) — sama ada upgrade atau downgrade.
     * Route: GET /owner/subscription/{plan}/checkout
     */
    public function checkout(SubscriptionPlan $plan)
    {
        $owner = Auth::user()->owner;

        // Upgrade (harga lebih tinggi) = kuatkuasa SERTA-MERTA (mula hari ini).
        // Downgrade/sama harga = DIJADUALKAN bermula lepas pakej sedia ada tamat.
        $startDate = $owner->isUpgrade($plan) ? today()->copy() : $owner->nextSubscriptionStartDate();
        $isScheduled = ! $startDate->isToday();

        if (! $owner->isOnlineRenewal()) {
            return view('owner.subscription.checkout', compact('plan', 'startDate', 'isScheduled'));
        }

        $endDate = $startDate->copy()->addDays($plan->duration_days);

        $subscription = Subscription::create([
            'owner_id' => $owner->id,
            'plan_id' => $plan->id,
            'agent_id' => $owner->agent_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount_paid' => $plan->price,
            'status' => 'pending', // Menunggu bayaran — belum 'active'/'scheduled' lagi.
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
            '1' => 'Pembayaran berjaya! Pakej anda akan aktif/berjadual dalam beberapa saat.',
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

        Log::info('ToyyibPay callback: SELESAI - subscription diaktifkan/dijadualkan', ['subscriptionId' => $subscription->id]);

        return response('ok', 200);
    }

    /**
     * MOD OFFLINE sahaja — sahkan terus tanpa payment gateway. Turut hormat
     * penjadualan (kalau ada pakej sedia ada yang masih berkuat kuasa).
     * Route: POST /owner/subscription/{plan}/confirm
     */
    public function confirm(Request $request, SubscriptionPlan $plan)
    {
        $owner = Auth::user()->owner;

        abort_if($owner->isOnlineRenewal(), 403, 'Akaun ini disetkan untuk pembayaran online sahaja.');

        $startDate = $owner->isUpgrade($plan) ? today()->copy() : $owner->nextSubscriptionStartDate();
        $endDate = $startDate->copy()->addDays($plan->duration_days);

        $subscription = Subscription::create([
            'owner_id' => $owner->id,
            'plan_id' => $plan->id,
            'agent_id' => $owner->agent_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount_paid' => $plan->price,
            'status' => 'pending',
        ]);

        $this->activateSubscription($subscription, 'offline');

        $message = $startDate->isToday()
            ? 'Pembayaran berjaya! Pakej "' . $plan->name . '" kini aktif.'
            : 'Pembayaran berjaya! Pakej "' . $plan->name . '" akan bermula pada ' . $startDate->format('d M Y') . ' (selepas pakej semasa tamat).';

        return redirect()->route('owner.dashboard')->with('success', $message);
    }

    private function verifyCallbackHash(Request $request): bool
    {
        $receivedHash = $request->input('hash');

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

    private function resolveSubscriptionFromRef(?string $refNo): ?Subscription
    {
        if (! $refNo || ! str_starts_with($refNo, 'SUB')) {
            return null;
        }

        return Subscription::find((int) str_replace('SUB', '', $refNo));
    }

    /**
     * LOGIK PENGAKTIFAN DIKONGSI — dipanggil dari callback ToyyibPay ATAU
     * dari admin secara manual. TIDAK overwrite start_date/end_date (dah
     * ditetapkan betul semasa checkout()/confirm() ikut jadual) — cuma
     * tetapkan status yang BETUL: 'active' kalau start_date dah sampai/lepas,
     * 'scheduled' kalau start_date masih masa hadapan.
     */
    public function activateSubscription(Subscription $subscription, string $gateway): void
    {
        DB::transaction(function () use ($subscription, $gateway) {
            $owner = $subscription->owner;
            $plan = $subscription->plan;

            $finalStatus = $subscription->start_date->isFuture() ? 'scheduled' : 'active';

            // Kalau ni berkuat kuasa SERTA-MERTA (upgrade), "gantikan" mana-mana
            // subscription lain (aktif ATAU berjadual) yang tarikhnya bertindih —
            // elak dua pakej berkuat kuasa serentak yang mengelirukan had cawangan/kerusi.
            if ($finalStatus === 'active') {
                $this->supersedeOverlapping($subscription);
            }

            $subscription->update(['status' => $finalStatus]);

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

            $scheduleNote = $finalStatus === 'scheduled' ? " (BERJADUAL mula {$subscription->start_date->format('d M Y')})" : ' (SERTA-MERTA)';

            ActivityLog::record(
                'subscription_selected',
                "Owner \"{$owner->business_name}\" pilih pakej {$plan->name} ({$gateway}, RM{$plan->price}){$scheduleNote}",
                $subscription,
                ['plan' => $plan->name, 'amount' => $plan->price, 'gateway' => $gateway, 'status' => $finalStatus]
            );
        });
    }

    /**
     * "Gantikan" subscription LAIN (aktif/berjadual) yang tarikhnya bertindih
     * dengan $newSub — dipanggil bila $newSub berkuat kuasa SERTA-MERTA (upgrade).
     * Kalau subscription lain tu SEPENUHNYA di masa hadapan (cth: downgrade yang
     * dah dijadualkan sebelum ni), batalkan terus. Kalau ia sedang berkuat kuasa
     * sekarang, potong pendek end_date dia setakat semalam sahaja.
     */
    private function supersedeOverlapping(Subscription $newSub): void
    {
        $others = $newSub->owner->subscriptions()
            ->whereIn('status', ['active', 'scheduled'])
            ->where('id', '!=', $newSub->id)
            ->where('end_date', '>=', $newSub->start_date)
            ->get();

        foreach ($others as $other) {
            if ($other->start_date->gte($newSub->start_date)) {
                $other->update(['status' => 'cancelled']);
            } else {
                $other->update(['end_date' => $newSub->start_date->copy()->subDay()]);
            }
        }
    }
}
