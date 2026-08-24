@extends('layouts.site')

@section('title', 'Subscription Saya')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Subscription Saya</h4>

@if ($currentSubscription)
    <div class="card card-brand mb-3" style="border-color:var(--green-ok) !important;">
        <div class="card-body p-4">
            <p class="text-muted small mb-1">Pakej Aktif Sekarang</p>
            <h5 class="font-display mb-2">
                {{ $currentSubscription->plan->name }}
                @if ($currentSubscription->is_trial)
                    <span class="badge text-bg-warning">Percubaan</span>
                @endif
            </h5>
            <p class="mb-0">
                Sah sehingga <strong>{{ $currentSubscription->end_date->format('d M Y') }}</strong>
                ({{ now()->diffInDays($currentSubscription->end_date, false) }} hari lagi)
            </p>
        </div>
    </div>
@else
    <div class="alert mb-3" style="background:var(--paper); border:1px solid var(--brass);">
        Anda belum ada subscription aktif. Pilih pakej di bawah untuk aktifkan penuh ciri sistem.
    </div>
@endif

@if ($upcomingSubscription)
    <div class="alert d-flex align-items-center gap-2 mb-5" style="background:var(--pine-light,#EAF1FC); border:1px solid var(--pine);">
        <i class="bi bi-calendar-event" style="color:var(--pine);"></i>
        <span>
            Pakej <strong>{{ $upcomingSubscription->plan->name }}</strong> sudah dijadualkan untuk bermula pada
            <strong>{{ $upcomingSubscription->start_date->format('d M Y') }}</strong> (gantikan pakej semasa selepas tamat).
        </span>
    </div>
@else
    <div class="mb-5"></div>
@endif

<h5 class="font-display mb-3">{{ $currentSubscription ? 'Upgrade / Downgrade Pakej' : 'Pilih Pakej' }}</h5>
@if ($currentSubscription && ! $upcomingSubscription)
    <p class="text-muted small mb-3">
        <strong>Upgrade</strong> (pakej lebih mahal) akan <strong>terus berkuat kuasa</strong> hari ini.
        <strong>Downgrade</strong> (pakej sama/lebih murah) akan <strong>dijadualkan</strong> bermula selepas
        pakej semasa tamat ({{ $currentSubscription->end_date->copy()->addDay()->format('d M Y') }}).
    </p>
@endif

<div class="row g-4">
    @foreach ($plans as $plan)
        <div class="col-md-4">
            <div class="card card-brand h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="font-display text-center mb-1">{{ $plan->name }}</h5>
                    <p class="text-center mb-3">
                        <span class="font-display" style="font-size:2rem; color:var(--pine);">RM{{ number_format($plan->price, 0) }}</span>
                        <span class="text-muted small">/ {{ $plan->duration_days }} hari</span>
                    </p>
                    <ul class="list-unstyled mb-4 flex-grow-1 small">
                        <li class="mb-2"><strong style="color:var(--pine);">🏪 {{ $plan->max_branches }} cawangan</strong></li>
                        <li class="mb-3"><strong style="color:var(--brass);">✂️ Sehingga {{ $plan->max_barbers }} kerusi{{ $plan->is_per_branch_limit ? ' / cawangan' : '' }}</strong></li>
                        @foreach (explode("\n", $plan->features) as $feature)
                            <li class="mb-2 text-muted">✓ {{ $feature }}</li>
                        @endforeach
                    </ul>
                    @if ($upcomingSubscription && $upcomingSubscription->plan_id === $plan->id)
                        <button class="btn btn-outline-secondary w-100 py-2" disabled>Sudah Dijadualkan</button>
                    @else
                        <a href="{{ route('owner.subscription.checkout', $plan) }}" class="btn btn-brand w-100 py-2">
                            {{ $currentSubscription && $currentSubscription->plan_id === $plan->id ? 'Perbaharui' : 'Pilih Pakej Ini' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
