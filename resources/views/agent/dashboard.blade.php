@extends('layouts.site')

@section('title', 'Dashboard Ejen')

@section('navbar')
    <x-agent-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-1 mt-3">
    <div>
        <h4 class="font-display mb-1">Hai, {{ auth()->user()->name }}</h4>
        <p class="text-muted mb-0">
            Kod Referral Anda:
            <span class="badge badge-ticket fs-6">{{ $agent->agent_code }}</span>
        </p>
    </div>
    <a href="{{ route('agent.register-owner.create') }}" class="btn btn-brand">+ Daftar Barbershop Baru</a>
</div>
<p class="text-muted small mb-4">
    Kongsi kod referral di atas kepada bakal owner semasa mereka daftar sendiri di halaman pendaftaran,
    atau daftarkan terus barbershop untuk mereka guna butang di atas.
</p>

@if ($expiringOwners->isNotEmpty())
    <div class="alert d-flex align-items-center gap-2" style="background:var(--red-light); border:1px solid var(--red); border-radius:12px;">
        <i class="bi bi-exclamation-triangle-fill" style="color:var(--red);"></i>
        <span>
            <strong>{{ $expiringOwners->count() }} owner subscription tamat/hampir tamat:</strong>
            {{ $expiringOwners->pluck('business_name')->implode(', ') }}
            — hubungi mereka untuk perbaharui pakej.
        </span>
    </div>
@endif

<div class="row g-3 mb-5">
    <div class="col-md-4">
        <x-stat-tile icon="bi-building" color="blue" label="Owner Direferral" value="{{ $referredOwners->count() }}" />
    </div>
    <div class="col-md-4">
        <x-stat-tile icon="bi-cash-coin" color="green" label="Komisen Diterima" value="RM{{ number_format($commissionPaid, 2) }}" />
    </div>
    <div class="col-md-4">
        <x-stat-tile icon="bi-hourglass-split" color="gold" label="Komisen Tertunggak" value="RM{{ number_format($commissionPending, 2) }}" />
    </div>
</div>

<h5 class="font-display mb-3">Owner Yang Anda Referral</h5>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Nama Perniagaan</th>
                    <th>Nama Owner</th>
                    <th>Tarikh Daftar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($referredOwners as $owner)
                    <tr>
                        <td>{{ $owner->business_name }}</td>
                        <td class="small text-muted">{{ $owner->user->name }}</td>
                        <td class="small text-muted">{{ $owner->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $owner->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $owner->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Belum ada owner yang daftar guna kod referral anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>

@if ($commissionPaid == 0 && $commissionPending == 0)
    <p class="text-muted small text-center mt-4">
        Komisen akan dijana secara automatik sebaik sahaja owner yang anda referral membuat pembayaran subscription
        (ciri pembayaran subscription akan datang tidak lama lagi).
    </p>
@endif
@endsection
