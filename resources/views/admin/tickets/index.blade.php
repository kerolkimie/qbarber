@extends('layouts.site')

@section('title', 'Senarai No. Tiket')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Senarai No. Tiket</h4>

<form method="GET" action="{{ route('admin.tickets.index') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <label class="fw-semibold small mb-0">Cawangan:</label>
        <select name="branch_id" class="form-select" style="max-width:260px;">
            <option value="">Semua Cawangan</option>
            @foreach ($branches as $b)
                <option value="{{ $b->id }}" {{ (string) $branchId === (string) $b->id ? 'selected' : '' }}>
                    {{ $b->name }} ({{ $b->owner->business_name ?? '—' }})
                </option>
            @endforeach
        </select>

        <label class="fw-semibold small mb-0">Status:</label>
        <select name="status" class="form-select" style="max-width:180px;">
            <option value="">Semua Status</option>
            <option value="waiting" {{ $status === 'waiting' ? 'selected' : '' }}>Waiting</option>
            <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled (Skip)</option>
        </select>

        <button type="submit" class="btn btn-pine btn-sm">Tapis</button>
    </div>
</form>

<div class="card card-brand mb-3">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Tiket</th>
                    <th>Cawangan</th>
                    <th>Owner</th>
                    <th>Servis</th>
                    <th>Tukang Gunting</th>
                    <th>Status</th>
                    <th>Tarikh</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr>
                        <td>#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="small">{{ $ticket->branch->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $ticket->branch->owner->business_name ?? '—' }}</td>
                        <td class="small">{{ $ticket->service->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $ticket->barber->name ?? '—' }}</td>
                        <td>
                            @php
                                $badgeClass = match($ticket->status) {
                                    'completed' => 'text-bg-success',
                                    'cancelled' => 'text-bg-danger',
                                    'in_progress' => 'text-bg-warning',
                                    default => 'text-bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-capitalize">
                                {{ $ticket->status === 'cancelled' ? 'Skip / Cancelled' : str_replace('_', ' ', $ticket->status) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $ticket->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tiada tiket ditemui.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>

{{ $tickets->links('pagination::bootstrap-5') }}
@endsection
