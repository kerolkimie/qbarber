@extends('layouts.site')

@section('title', 'QR Code — ' . $branch->name)

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3 d-print-none">
    <a href="{{ route('owner.branches.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali</a>
    <button onclick="window.print()" class="btn btn-brand btn-sm">🖨 Cetak QR Code</button>
</div>

<div class="d-flex justify-content-center">
    <div class="text-center p-5" style="background:#fff; border:2px solid var(--pine); border-radius:14px; max-width:480px;">
        <p class="eyebrow mb-1" style="color:var(--red); font-family:'JetBrains Mono',monospace; letter-spacing:2px; font-size:.75rem; text-transform:uppercase;">
            Imbas Untuk Ambil Nombor Giliran
        </p>
        <h3 class="font-display mb-4">{{ $branch->name }}</h3>

        <img src="https://api.qrserver.com/v1/create-qr-code/?size=320x320&data={{ urlencode($qrUrl) }}"
             alt="QR Code {{ $branch->name }}" class="img-fluid mb-4" style="border:8px solid var(--paper); border-radius:8px;">

        <p class="text-muted small mb-0">{{ $qrUrl }}</p>
    </div>
</div>

<p class="text-muted small text-center mt-4 d-print-none">
    Cetak dan letak QR code ni di kaunter/pintu masuk kedai supaya pelanggan boleh terus imbas & ambil nombor giliran.
</p>
@endsection

@push('styles')
<style>
    @media print {
        .navbar-brand-bar, footer, .d-print-none { display: none !important; }
        body { background: #fff !important; }
    }
</style>
@endpush
