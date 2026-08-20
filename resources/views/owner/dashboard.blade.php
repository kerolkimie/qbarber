@extends('layouts.site')

@section('title', 'Dashboard Owner')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')

@if (! $subscription)
    <div class="alert d-flex align-items-center gap-2 mt-3" style="background:var(--brass-light); border:1px solid var(--brass); border-radius:12px;">
        <i class="bi bi-info-circle-fill" style="color:var(--brass);"></i>
        <span>Akaun anda belum ada subscription aktif. <a href="{{ route('owner.subscription.index') }}" class="fw-semibold text-decoration-none">Pilih pakej sekarang</a> untuk buka penuh ciri sistem.</span>
    </div>
@elseif (! $subscriptionValid)
    <div class="alert d-flex justify-content-between align-items-center mt-3" style="background:var(--red-light); border:1px solid var(--red); border-radius:12px;">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill" style="color:var(--red);"></i>
            Pakej <strong>{{ $subscription->plan->name }}</strong> anda telah <strong>tamat tempoh</strong> pada {{ $subscription->end_date->format('d M Y') }}.
            Sistem giliran (scan QR) tidak boleh diguna pelanggan sehingga diperbaharui.
        </span>
        <a href="{{ route('owner.subscription.index') }}" class="btn btn-sm btn-brand">Perbaharui Sekarang</a>
    </div>
@else
    <div class="alert d-flex justify-content-between align-items-center mt-3" style="background:var(--green-light); border:1px solid var(--green-ok); border-radius:12px;">
        <span class="d-flex align-items-center gap-2">
            <i class="bi bi-patch-check-fill" style="color:var(--green-ok);"></i>
            Pakej <strong>{{ $subscription->plan->name }}</strong> aktif sehingga
            <strong>{{ $subscription->end_date->format('d M Y') }}</strong>.
        </span>
        <a href="{{ route('owner.subscription.index') }}" class="btn btn-sm btn-outline-secondary">Urus Subscription</a>
    </div>
@endif

<h4 class="font-display mb-4 mt-3">Ringkasan {{ $owner->business_name }}</h4>

<div class="row g-3 mb-5">
    <div class="col-md-3">
        <x-stat-tile icon="bi-shop" color="blue" label="Cawangan" value="{{ $branches->count() }} / {{ $branchLimit }}" href="{{ route('owner.branches.index') }}" />
    </div>
    <div class="col-md-3">
        <x-stat-tile icon="bi-scissors" color="gold" label="Kerusi (Tukang Gunting)" value="{{ $chairUsed }} / {{ $chairLimit }}{{ $isPerBranchChairLimit ? ' /cwg' : '' }}" href="{{ route('owner.barbers.index') }}" />
    </div>
    <div class="col-md-3">
        <x-stat-tile icon="bi-check-circle" color="green" label="Dilayan Hari Ini" value="{{ $todayServed }}" href="{{ route('owner.tickets.served') }}" />
    </div>
    <div class="col-md-3">
        <x-stat-tile icon="bi-hourglass-split" color="blue" label="Menunggu Sekarang" value="{{ $todayWaiting }}" href="{{ route('owner.tickets.waiting') }}" />
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="font-display mb-0">QR Code Cawangan</h5>
</div>
<div class="row g-3 mb-5">
    @forelse ($branches as $branch)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('owner.branches.qr', $branch) }}" class="text-decoration-none text-reset">
                <div class="card card-brand h-100 text-center">
                    <div class="card-body p-3">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url('/q/' . $branch->qr_token)) }}"
                             alt="QR {{ $branch->name }}" class="mb-2" style="border-radius:8px;">
                        <div class="fw-semibold small text-truncate">{{ $branch->name }}</div>
                        <div class="text-muted small">Klik untuk cetak</div>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <p class="text-muted small">Tambah cawangan dulu untuk jana QR code.</p>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-between align-items-center mb-1">
    <h5 class="font-display mb-0">Cawangan Anda</h5>
    <a href="{{ route('owner.branches.create') }}" class="btn btn-brand btn-sm">+ Tambah Cawangan</a>
</div>
<p class="text-muted small mb-3">
    @if ($branches->count() <= 1)
        Ada satu kedai je? Cawangan default anda dah sedia — cuma perlu tambah cawangan baru kalau ada lokasi kedua.
    @else
        Anda ada {{ $branches->count() }} cawangan aktif.
    @endif
</p>

<div class="card card-brand mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0">
            <thead>
                <tr><th>Cawangan</th><th>Tukang Gunting</th><th>Servis</th><th>Status</th><th class="text-end">Tindakan</th></tr>
            </thead>
            <tbody>
                @forelse ($branches as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td>{{ $branch->barbers_count }}</td>
                        <td>{{ $branch->services_count }}</td>
                        <td><span class="badge text-bg-secondary text-capitalize">{{ $branch->status }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('owner.branches.qr', $branch) }}" class="btn btn-sm btn-brand">📱 QR</a>
                            <a href="{{ route('owner.branches.barbers.index', $branch) }}" class="btn btn-sm btn-pine">Tukang Gunting</a>
                            <a href="{{ route('owner.branches.services.index', $branch) }}" class="btn btn-sm btn-pine">Servis</a>
                            <a href="{{ route('owner.branches.edit', $branch) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-muted text-center py-4">
                            Belum ada cawangan lagi. Klik "+ Tambah Cawangan" untuk mula.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="font-display mb-0">Dilayan Hari Ini</h5>
            <a href="{{ route('owner.tickets.served') }}" class="small text-decoration-none">Lihat Semua &rarr;</a>
        </div>
        <div class="card card-brand">
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Tiket</th><th>Servis</th><th>Tukang Gunting</th><th>Masa</th></tr></thead>
                    <tbody>
                        @forelse ($recentServed as $t)
                            <tr>
                                <td>#{{ str_pad($t->ticket_number, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="small">{{ $t->service->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $t->barber->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $t->completed_at->format('h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada yang dilayan hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="font-display mb-0">Menunggu / Sedang Dilayan</h5>
            <a href="{{ route('owner.tickets.waiting') }}" class="small text-decoration-none">Lihat Semua &rarr;</a>
        </div>
        <div class="card card-brand">
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Tiket</th><th>Servis</th><th>Tukang Gunting</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($recentWaiting as $t)
                            <tr>
                                <td>#{{ str_pad($t->ticket_number, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="small">{{ $t->service->name ?? '—' }}</td>
                                <td class="small text-muted">{{ $t->barber->name ?? 'Belum dipanggil' }}</td>
                                <td>
                                    <span class="badge {{ $t->status === 'in_progress' ? 'text-bg-warning' : 'text-bg-secondary' }} text-capitalize">
                                        {{ str_replace('_', ' ', $t->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Tiada sesiapa menunggu buat masa ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
