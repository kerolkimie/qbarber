@extends('layouts.site')

@section('title', 'Semua Tukang Gunting')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Semua Tukang Gunting</h4>

<form method="GET" action="{{ route('owner.barbers.index') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <input type="text" name="q" value="{{ $q }}" class="form-control" style="max-width:280px;" placeholder="Cari nama tukang gunting / cawangan">
        <button type="submit" class="btn btn-pine btn-sm">Cari</button>
        @if ($q)
            <a href="{{ route('owner.barbers.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        @endif
    </div>
</form>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr><th>Nama</th><th>Cawangan</th><th>Emel</th><th>Model Bayaran</th><th>Status</th><th>Semasa</th></tr>
            </thead>
            <tbody>
                @forelse ($barbers as $barber)
                    <tr>
                        <td>{{ $barber->name }}</td>
                        <td class="small text-muted">{{ $barber->branch->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $barber->user->email ?? '—' }}</td>
                        <td class="small">{{ $barber->isChairRental() ? 'Sewa Kerusi' : 'Komisen' }}</td>
                        <td><span class="badge {{ $barber->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">{{ $barber->status }}</span></td>
                        <td><span class="badge {{ $barber->current_state === 'available' ? 'text-bg-success' : ($barber->current_state === 'busy' ? 'text-bg-danger' : 'text-bg-secondary') }} text-capitalize">{{ $barber->current_state }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada tukang gunting.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
