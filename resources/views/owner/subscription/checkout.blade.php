@extends('layouts.site')

@section('title', 'Sahkan Pembayaran')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Sahkan Pembayaran</div>
            <div class="card-body p-4">

                @if ($isScheduled)
                    <div class="alert alert-warning small">
                        📅 Pakej semasa anda masih aktif. Pakej <strong>{{ $plan->name }}</strong> ni akan
                        <strong>DIJADUALKAN bermula {{ $startDate->format('d M Y') }}</strong> (bukan serta-merta) —
                        pakej sedia ada anda kekal berkuat kuasa sehingga tarikh tu.
                    </div>
                @else
                    <div class="alert alert-warning small">
                        ⚠️ Ini simulasi pembayaran (belum ada gateway sebenar dipasang seperti
                        Billplz/ToyyibPay/Stripe). Klik "Sahkan" untuk terus aktifkan pakej.
                    </div>
                @endif

                <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
                    <div>
                        <p class="font-display mb-1">{{ $plan->name }}</p>
                        <p class="text-muted small mb-0">{{ $plan->duration_days }} hari</p>
                    </div>
                    <p class="font-display fs-4 mb-0">RM{{ number_format($plan->price, 2) }}</p>
                </div>

                <ul class="list-unstyled mb-4 small">
                    <li class="mb-2"><strong style="color:var(--pine);">🏪 {{ $plan->max_branches }} cawangan</strong></li>
                    <li class="mb-3"><strong style="color:var(--brass);">✂️ Sehingga {{ $plan->max_barbers }} kerusi{{ $plan->is_per_branch_limit ? ' / cawangan' : '' }}</strong></li>
                    @foreach (explode("\n", $plan->features) as $feature)
                        <li class="mb-2 text-muted">✓ {{ $feature }}</li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('owner.subscription.confirm', $plan) }}">
                    @csrf
                    <button type="submit" class="btn btn-brand w-100 py-2">Sahkan Pembayaran — RM{{ number_format($plan->price, 2) }}</button>
                </form>

                <a href="{{ route('owner.subscription.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
            </div>
        </div>
    </div>
</div>
@endsection
