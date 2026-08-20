@extends('layouts.site')

@section('title', 'Semua Subscription')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Semua Subscription</h4>

<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Owner</th>
                    <th>Pakej</th>
                    <th>Ejen</th>
                    <th>Tarikh Mula</th>
                    <th>Tarikh Tamat</th>
                    <th>Jumlah Bayar</th>
                    <th>Status Bayaran</th>
                    <th>Status</th>
                    <th class="text-end">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subscriptions as $sub)
                    <tr>
                        <td>{{ $sub->owner->business_name ?? '—' }}</td>
                        <td>{{ $sub->plan->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $sub->agent->user->name ?? '—' }}</td>
                        <td class="small text-muted">{{ $sub->start_date->format('d M Y') }}</td>
                        <td class="small text-muted">{{ $sub->end_date->format('d M Y') }}</td>
                        <td>RM{{ number_format($sub->amount_paid, 2) }}</td>
                        <td>
                            @php $payment = $sub->payments->first(); @endphp
                            <span class="badge {{ $payment && $payment->status === 'success' ? 'text-bg-success' : 'text-bg-warning' }} text-capitalize">
                                {{ $payment->status ?? 'tiada' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $sub->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                                {{ $sub->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if ($sub->status !== 'active' && $sub->toyyibpay_bill_code)
                                <form method="POST" action="{{ route('admin.subscriptions.recheck', $sub) }}"
                                      onsubmit="return confirm('Semak status bill ni terus dari ToyyibPay?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-brand">Semak &amp; Aktifkan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada subscription.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
