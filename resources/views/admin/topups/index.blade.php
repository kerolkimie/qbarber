@extends('layouts.site')

@section('title', 'Transaksi Topup Point')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3 flex-wrap gap-2">
    <h4 class="font-display mb-0">Transaksi Topup Point</h4>
    <a href="{{ route('admin.topup-packages.index') }}" class="btn btn-outline-secondary btn-sm">Urus Pakej Topup</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <x-stat-tile icon="bi-cash-stack" color="green" label="Jumlah Hasil Topup" value="RM{{ number_format($totalRevenue, 2) }}" />
    </div>
    <div class="col-md-4">
        <x-stat-tile icon="bi-receipt" color="blue" label="Jumlah Transaksi" value="{{ $topups->total() }}" />
    </div>
</div>

<form method="GET" action="{{ route('admin.topups.index') }}" class="card card-brand mb-4">
    <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
        <label class="fw-semibold small mb-0">Owner:</label>
        <select name="owner_id" class="form-select" style="max-width:260px;">
            <option value="">Semua Owner</option>
            @foreach ($owners as $o)
                <option value="{{ $o->id }}" {{ (string) $ownerId === (string) $o->id ? 'selected' : '' }}>{{ $o->business_name }}</option>
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
                    <th>Tarikh</th>
                    <th>Owner</th>
                    <th>Pakej</th>
                    <th>Point</th>
                    <th>Harga</th>
                    <th>Baki Sekarang</th>
                    <th>Luput</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topups as $topup)
                    <tr>
                        <td class="small text-muted">{{ $topup->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $topup->owner->business_name ?? '—' }}</td>
                        <td class="small text-muted">
                            {{ $topup->topupPackage ? $topup->topupPackage->points . ' point pakej' : '—' }}
                        </td>
                        <td>{{ $topup->points_total }}</td>
                        <td>RM{{ number_format($topup->price_paid, 2) }}</td>
                        <td class="small text-muted">{{ $topup->points_remaining }} tinggal</td>
                        <td class="small text-muted">{{ $topup->expires_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tiada transaksi topup lagi.</td></tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>

{{ $topups->links('pagination::bootstrap-5') }}
@endsection
