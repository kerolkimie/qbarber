@extends('layouts.site')

@section('title', $owner->business_name)

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<a href="{{ route('admin.owners.index') }}" class="btn btn-outline-secondary btn-sm mt-3 mb-3">&larr; Kembali</a>

<h4 class="font-display mb-1">{{ $owner->business_name }}</h4>
<p class="text-muted mb-4">{{ $owner->user->name }} &middot; {{ $owner->user->email }}</p>

<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="card card-brand h-100">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Cawangan</div>
                <div class="font-display fs-3">{{ $owner->branches->count() }} / {{ $owner->branchLimit() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-brand h-100">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Kerusi (Tukang Gunting)</div>
                <div class="font-display fs-3">{{ $owner->totalActiveBarbers() }} / {{ $owner->chairLimit() }}{{ $owner->isPerBranchChairLimit() ? ' /cwg' : '' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-brand h-100">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Ejen</div>
                <div class="font-display fs-6 mt-2">{{ $owner->agent->user->name ?? 'Tiada' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-brand h-100">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Mod Perbaharui Pakej</div>
                <span class="badge {{ $owner->renewal_mode === 'online' ? 'text-bg-success' : 'text-bg-secondary' }} mt-2 mb-2">
                    {{ $owner->renewal_mode === 'online' ? 'Online (ToyyibPay)' : 'Offline (Manual)' }}
                </span>
                <form method="POST" action="{{ route('admin.owners.renewalMode.update', $owner) }}">
                    @csrf
                    <input type="hidden" name="renewal_mode" value="{{ $owner->renewal_mode === 'online' ? 'offline' : 'online' }}">
                    <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                        Tukar ke {{ $owner->renewal_mode === 'online' ? 'Offline' : 'Online' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-brand h-100">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Status Akaun</div>
                <span class="badge {{ $owner->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize mt-2">{{ $owner->status }}</span>
                <div class="text-muted small mt-2 mb-1">Emel Disahkan</div>
                @if ($owner->user->hasVerifiedEmail())
                    <span class="badge text-bg-success">Ya</span>
                @else
                    <span class="badge text-bg-danger d-block mb-2">Belum</span>
                    <form method="POST" action="{{ route('admin.owners.activate', $owner) }}"
                          onsubmit="return confirm('Aktifkan akaun {{ $owner->business_name }} secara manual?');">
                        @csrf
                        <button type="submit" class="btn btn-brand btn-sm w-100">Aktifkan Manual</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<h5 class="font-display mb-3">Cawangan</h5>
<div class="card card-brand mb-5">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Nama</th><th>Komisen %</th><th>Tukang Gunting</th><th>Servis</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($owner->branches as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td>{{ rtrim(rtrim(number_format($branch->commission_percent, 2), '0'), '.') }}%</td>
                        <td>{{ $branch->barbers->count() }}</td>
                        <td>{{ $branch->services->count() }}</td>
                        <td><span class="badge text-bg-secondary text-capitalize">{{ $branch->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Tiada cawangan.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<h5 class="font-display mb-3">Sejarah Subscription</h5>
<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Pakej</th><th>Tarikh Mula</th><th>Tarikh Tamat</th><th>Jumlah Bayar</th><th>Status</th></tr></thead>
            <tbody>
                @forelse ($owner->subscriptions as $sub)
                    <tr>
                        <td>{{ $sub->plan->name }}</td>
                        <td>{{ $sub->start_date->format('d M Y') }}</td>
                        <td>{{ $sub->end_date->format('d M Y') }}</td>
                        <td>RM{{ number_format($sub->amount_paid, 2) }}</td>
                        <td><span class="badge text-bg-secondary text-capitalize">{{ $sub->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada subscription.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
