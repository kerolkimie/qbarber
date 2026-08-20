@extends('layouts.site')

@section('title', 'Admin Dashboard')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Ringkasan Sistem</h4>

<div class="row g-3 mb-5">
    <div class="col-md-4 col-lg-2">
        <x-stat-tile icon="bi-building" color="blue" label="Owner" value="{{ $stats['total_owners'] }}" href="{{ route('admin.owners.index') }}" />
    </div>
    <div class="col-md-4 col-lg-2">
        <x-stat-tile icon="bi-person-badge" color="gold" label="Agent" value="{{ $stats['total_agents'] }}" href="{{ route('admin.agents.index') }}" />
    </div>
    <div class="col-md-4 col-lg-2">
        <x-stat-tile icon="bi-shop" color="blue" label="Cawangan" value="{{ $stats['total_branches'] }}" href="{{ route('admin.branches.index') }}" />
    </div>
    <div class="col-md-4 col-lg-2">
        <x-stat-tile icon="bi-file-earmark-check" color="green" label="Subscription Aktif" value="{{ $stats['active_subscriptions'] }}" href="{{ route('admin.subscriptions.index') }}" />
    </div>
    <div class="col-md-4 col-lg-2">
        <x-stat-tile icon="bi-cash-stack" color="green" label="Jumlah Hasil" value="RM{{ number_format($stats['total_revenue'], 2) }}" />
    </div>
    <div class="col-md-4 col-lg-2">
        <x-stat-tile icon="bi-hourglass-split" color="red" label="Komisen Tertunggak" value="RM{{ number_format($stats['pending_commissions'], 2) }}" href="{{ route('admin.commissions.index') }}" />
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card card-brand">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span>Owner Terbaru</span>
                <a href="{{ route('admin.owners.index') }}" class="small text-decoration-none" style="color:var(--paper);">Lihat Semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
<table class="table mb-0">
                    <thead>
                        <tr><th>Perniagaan</th><th>Agent</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOwners as $owner)
                            <tr>
                                <td>{{ $owner->business_name }}</td>
                                <td class="small text-muted">{{ $owner->agent->agent_code ?? '—' }}</td>
                                <td><span class="badge text-bg-secondary text-capitalize">{{ $owner->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center py-3">Belum ada owner mendaftar.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-brand">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span>Subscription Terbaru</span>
                <a href="{{ route('admin.subscriptions.index') }}" class="small text-decoration-none" style="color:var(--paper);">Lihat Semua &rarr;</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
<table class="table mb-0">
                    <thead>
                        <tr><th>Owner</th><th>Pakej</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($recentSubscriptions as $sub)
                            <tr>
                                <td>{{ $sub->owner->business_name ?? '—' }}</td>
                                <td class="small text-muted">{{ $sub->plan->name ?? '—' }}</td>
                                <td><span class="badge text-bg-secondary text-capitalize">{{ $sub->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center py-3">Belum ada subscription lagi.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-md-3">
        <a href="{{ route('admin.branches.index') }}" class="btn btn-pine w-100 py-3">Semua Cawangan</a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.tickets.index') }}" class="btn btn-pine w-100 py-3">Senarai Tiket</a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.plans.index') }}" class="btn btn-pine w-100 py-3">Urus Pakej</a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.commissions.index') }}" class="btn btn-pine w-100 py-3">Komisen Ejen</a>
    </div>
</div>
@endsection
