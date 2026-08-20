@extends('layouts.site')

@section('title', 'Komisen Ejen')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Komisen Ejen</h4>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card card-brand h-100" style="border-color:var(--brass) !important;">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Jumlah Tertunggak</div>
                <div class="font-display fs-4">RM{{ number_format($totalPending, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-brand h-100" style="border-color:var(--green-ok) !important;">
            <div class="card-body text-center p-3">
                <div class="text-muted small mb-1">Jumlah Dibayar</div>
                <div class="font-display fs-4">RM{{ number_format($totalPaid, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Ejen</th>
                    <th>Owner</th>
                    <th>Pakej</th>
                    <th>Peratus</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($commissions as $commission)
                    <tr>
                        <td>{{ $commission->agent->user->name }}</td>
                        <td class="small text-muted">{{ $commission->subscription->owner->business_name ?? '—' }}</td>
                        <td class="small text-muted">{{ $commission->subscription->plan->name ?? '—' }}</td>
                        <td>{{ rtrim(rtrim(number_format($commission->percent, 2), '0'), '.') }}%</td>
                        <td class="fw-semibold">RM{{ number_format($commission->amount, 2) }}</td>
                        <td>
                            <span class="badge {{ $commission->status === 'paid' ? 'text-bg-success' : 'text-bg-warning' }} text-capitalize">
                                {{ $commission->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if ($commission->status === 'pending')
                                <form method="POST" action="{{ route('admin.commissions.markPaid', $commission) }}"
                                      onsubmit="return confirm('Tandakan komisen ni sebagai dibayar?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-brand">Tandakan Dibayar</button>
                                </form>
                            @else
                                <span class="text-muted small">{{ $commission->paid_at?->format('d M Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada komisen dijana. Komisen akan muncul di sini bila owner (yang didaftar melalui ejen) membuat pembayaran subscription.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
