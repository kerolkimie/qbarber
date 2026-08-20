@extends('layouts.site')

@section('title', 'Log Aktiviti')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Log Aktiviti Sistem</h4>
<p class="text-muted small mb-4">
    Rekod semua emel yang dihantar, pemilihan subscription, topup point, dan pembayaran komisen ejen.
</p>

<form method="GET" action="{{ route('admin.logs.index') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <label class="fw-semibold small mb-0">Jenis:</label>
        <select name="type" class="form-select" style="max-width:260px;">
            <option value="">Semua Jenis</option>
            @foreach ($types as $key => $label)
                <option value="{{ $key }}" {{ $type === $key ? 'selected' : '' }}>{{ $label }}</option>
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
                <tr>
                    <th>Jenis</th>
                    <th>Penerangan</th>
                    <th>Oleh</th>
                    <th>Tarikh</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    @php
                        $badgeClass = match($log->type) {
                            'email_sent' => 'text-bg-secondary',
                            'subscription_selected' => 'text-bg-success',
                            'point_topup' => 'text-bg-warning',
                            'commission_paid' => 'text-bg-primary',
                            'account_activated_manual' => 'text-bg-danger',
                            default => 'text-bg-secondary',
                        };
                    @endphp
                    <tr>
                        <td><span class="badge {{ $badgeClass }}">{{ \App\Models\ActivityLog::TYPE_LABELS[$log->type] ?? $log->type }}</span></td>
                        <td class="small">{{ $log->description }}</td>
                        <td class="small text-muted">{{ $log->causer->name ?? 'Sistem' }}</td>
                        <td class="small text-muted">{{ $log->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Tiada log lagi.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
{{ $logs->links('pagination::bootstrap-5') }}
@endsection
