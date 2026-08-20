@extends('layouts.site')

@section('title', 'Point Saya')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<h4 class="font-display mb-4 mt-3">Point Saya</h4>

@if ($lowPoints)
    <div class="alert d-flex justify-content-between align-items-center" style="background:#FBEFEF; border:1px solid var(--red);">
        <span>⚠️ Baki point anda dah rendah ({{ $balance }} / ambang {{ $threshold }}). Perbaharui/tambah pakej untuk elak gangguan servis.</span>
        <a href="{{ route('owner.subscription.index') }}" class="btn btn-sm btn-brand">Pilih Pakej</a>
    </div>
@endif

<div class="row g-3 mb-5">
    <div class="col-md-6">
        <div class="card card-brand h-100" style="{{ $lowPoints ? 'border-color:var(--red) !important;' : 'border-color:var(--green-ok) !important;' }}">
            <div class="card-body text-center p-4">
                <div class="text-muted small mb-1">Baki Point Semasa</div>
                <div class="font-display fs-1">{{ $balance }}</div>
                @if ($threshold)
                    <div class="text-muted small">Ambang alert: {{ $threshold }} point</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-brand h-100">
            <div class="card-body p-4">
                <p class="text-muted small mb-2">Cara Point Berfungsi</p>
                <ul class="small text-muted mb-0 ps-3">
                    <li>1 point ditolak setiap kali tukang gunting siapkan 1 tugasan</li>
                    <li>Point tak habis guna akan <strong>carry forward</strong> bila anda perbaharui pakej</li>
                    <li>Tarikh luput point ikut <strong>tempoh pakej</strong> yang anda pilih (cth: pakej 30 hari = point luput dalam 30 hari)</li>
                    <li>Bila baki capai <strong>separuh</strong> dari peruntukan pakej, owner &amp; ejen akan dapat alert</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card card-brand mb-5" style="background:var(--pine-light, #EAF1FC); border:1px solid var(--pine);">
    <div class="card-body p-4 text-center">
        <h6 class="font-display mb-2">Nak Tambah Point?</h6>
        <p class="text-muted small mb-3">Point ditambah melalui pemilihan/pembaharuan pakej subscription — tiada topup berasingan lagi.</p>
        <a href="{{ route('owner.subscription.index') }}" class="btn btn-brand">Lihat Pakej Subscription</a>
    </div>
</div>

<h5 class="font-display mb-3 d-flex justify-content-between align-items-center">
    Batch Point Aktif
    <a href="{{ route('owner.tickets.history') }}" class="btn btn-outline-secondary btn-sm">Lihat Sejarah Penggunaan &rarr;</a>
</h5>
<div class="card card-brand">
    <div class="card-body p-0">
        <div class="table-responsive">
<table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Sumber</th>
                    <th>Baki</th>
                    <th>Jumlah Asal</th>
                    <th>Tarikh Diberi</th>
                    <th>Luput Pada</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($batches as $batch)
                    <tr>
                        <td class="text-capitalize">
                            @if ($batch->source === 'subscription') Subscription Bulanan
                            @elseif ($batch->source === 'manual_admin') Diberi Oleh Admin
                            @else Topup
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $batch->points_remaining }}</td>
                        <td class="text-muted small">{{ $batch->points_total }}</td>
                        <td class="text-muted small">{{ $batch->granted_at->format('d M Y') }}</td>
                        <td class="text-muted small">{{ $batch->expires_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Tiada batch point aktif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        </div>
    </div>
</div>
@endsection
