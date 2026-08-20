@extends('layouts.site')

@section('title', 'Carian Owner')

@section('navbar')
    <x-agent-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Owner Bawah Anda</h4>

<form method="GET" action="{{ route('agent.owners.index') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <input type="text" name="q" value="{{ $search }}" class="form-control" style="max-width:320px;"
               placeholder="Cari nama perniagaan, nama owner, telefon, atau emel...">
        <button type="submit" class="btn btn-pine btn-sm">Cari</button>
        @if ($search)
            <a href="{{ route('agent.owners.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        @endif
    </div>
</form>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Perniagaan</th>
                    <th>No. Telefon</th>
                    <th>Status</th>
                    <th>Pakej</th>
                    <th>Tarikh Mula</th>
                    <th>Tarikh Tamat</th>
                    <th>Status Perbaharuan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($owners as $owner)
                    <tr>
                        <td>{{ $owner->business_name }}</td>
                        <td class="small">
                            @if ($owner->user->phone)
                                <a href="tel:{{ $owner->user->phone }}" class="text-decoration-none">{{ $owner->user->phone }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $owner->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $owner->status }}
                            </span>
                        </td>
                        <td class="small">{{ $owner->activeSubscription->plan->name ?? 'Tiada' }}</td>
                        <td class="small text-muted">{{ $owner->activeSubscription->start_date->format('d M Y') ?? '—' }}</td>
                        <td class="small text-muted">{{ $owner->activeSubscription->end_date->format('d M Y') ?? '—' }}</td>
                        <td>
                            @if ($owner->subscription_expiring)
                                <span class="badge text-bg-danger">⚠ Tamat/Hampir Tamat</span>
                                <br><span class="text-danger small">Hubungi segera</span>
                            @else
                                <span class="badge text-bg-success">Aktif</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tiada owner ditemui.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
