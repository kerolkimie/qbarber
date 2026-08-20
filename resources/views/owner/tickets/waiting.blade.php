@extends('layouts.site')

@section('title', 'Menunggu Sekarang')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Menunggu / Sedang Dilayan</h4>

<form method="GET" action="{{ route('owner.tickets.waiting') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <label class="fw-semibold small mb-0">Cawangan:</label>
        <select name="branch_id" class="form-select" style="max-width:240px;">
            <option value="">Semua Cawangan</option>
            @foreach ($branches as $b)
                <option value="{{ $b->id }}" {{ (string) $branchId === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-pine btn-sm">Tapis</button>
    </div>
</form>

<div class="card card-brand mb-3">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr><th>Tiket</th><th>Cawangan</th><th>Servis</th><th>Tukang Gunting</th><th>Status</th><th>Sejak</th></tr>
            </thead>
            <tbody>
                @forelse ($tickets as $t)
                    <tr>
                        <td>#{{ str_pad($t->ticket_number, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="small text-muted">{{ $t->branch->name ?? '—' }}</td>
                        <td>{{ $t->service->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $t->barber->name ?? 'Belum dipanggil' }}</td>
                        <td>
                            <span class="badge {{ $t->status === 'in_progress' ? 'text-bg-warning' : 'text-bg-secondary' }} text-capitalize">
                                {{ str_replace('_', ' ', $t->status) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $t->created_at->format('h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tiada sesiapa menunggu buat masa ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
{{ $tickets->links('pagination::bootstrap-5') }}
@endsection
