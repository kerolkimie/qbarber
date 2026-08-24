@extends('layouts.site')

@section('title', 'Dilayan Hari Ini')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Dilayan Hari Ini</h4>

<form method="GET" action="{{ route('owner.tickets.served') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <label class="fw-semibold small mb-0">Cawangan:</label>
        <select name="branch_id" class="form-select" style="max-width:240px;">
            <option value="">Semua Cawangan</option>
            @foreach ($branches as $b)
                <option value="{{ $b->id }}" {{ (string) $branchId === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        <input type="text" name="q" value="{{ $q }}" class="form-control" style="max-width:220px;" placeholder="Cari no. tiket / servis / tukang gunting">
        <button type="submit" class="btn btn-pine btn-sm">Tapis</button>
        @if ($q || $branchId)
            <a href="{{ route('owner.tickets.served') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        @endif
    </div>
</form>

<div class="card card-brand mb-3">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr><th>Tiket</th><th>Cawangan</th><th>Servis</th><th>Tukang Gunting</th><th>Masa Siap</th></tr>
            </thead>
            <tbody>
                @forelse ($tickets as $t)
                    <tr>
                        <td>#{{ str_pad($t->ticket_number, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="small text-muted">{{ $t->branch->name ?? '—' }}</td>
                        <td>{{ $t->service->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $t->barber->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $t->completed_at->format('h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada yang dilayan hari ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
{{ $tickets->links('pagination::bootstrap-5') }}
@endsection
