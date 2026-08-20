@extends('layouts.site')

@section('title', 'Pendapatan Saya')

@section('navbar')
<nav class="navbar navbar-brand-bar navbar-dark py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('barber.dashboard') }}">
            <span class="pole-dot"></span> Blade &amp; Fade
        </a>
        <a href="{{ route('barber.dashboard') }}" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
    </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <h4 class="font-display mb-3 mt-3">Pendapatan Saya</h4>

        {{-- Tab Hari / Minggu / Bulan --}}
        <div class="btn-group mb-4 w-100" role="group">
            <a href="{{ route('barber.earnings', ['period' => 'day']) }}"
               class="btn {{ $period === 'day' ? 'btn-brand' : 'btn-outline-secondary' }}">Hari</a>
            <a href="{{ route('barber.earnings', ['period' => 'week']) }}"
               class="btn {{ $period === 'week' ? 'btn-brand' : 'btn-outline-secondary' }}">Minggu</a>
            <a href="{{ route('barber.earnings', ['period' => 'month']) }}"
               class="btn {{ $period === 'month' ? 'btn-brand' : 'btn-outline-secondary' }}">Bulan</a>
        </div>

        {{-- Penapis tarikh ikut jenis tab --}}
        <form method="GET" action="{{ route('barber.earnings') }}" class="card card-brand mb-4">
            <div class="card-body p-3 d-flex align-items-center gap-2 flex-wrap">
                <input type="hidden" name="period" value="{{ $period }}">

                @if ($period === 'day')
                    <label class="fw-semibold small mb-0">Pilih Hari:</label>
                    <input type="date" name="date" value="{{ $inputValue }}" class="form-control" style="max-width:200px;">
                @elseif ($period === 'week')
                    <label class="fw-semibold small mb-0">Pilih Sebarang Tarikh Dalam Minggu:</label>
                    <input type="date" name="date" value="{{ $inputValue }}" class="form-control" style="max-width:200px;">
                @else
                    <label class="fw-semibold small mb-0">Pilih Bulan:</label>
                    <input type="month" name="month" value="{{ $inputValue }}" class="form-control" style="max-width:200px;">
                @endif

                <button type="submit" class="btn btn-pine btn-sm">Tapis</button>

                <div class="ms-auto d-flex gap-2">
                    <a href="{{ route('barber.earnings', ['period' => $period, ($period === 'month' ? 'month' : 'date') => $prevAnchor]) }}"
                       class="btn btn-outline-secondary btn-sm">&larr; Sebelum</a>
                    <a href="{{ route('barber.earnings', ['period' => $period, ($period === 'month' ? 'month' : 'date') => $nextAnchor]) }}"
                       class="btn btn-outline-secondary btn-sm">Seterusnya &rarr;</a>
                </div>
            </div>
        </form>

        {{-- Ringkasan --}}
        @if ($barber->isChairRental())
            <div class="alert mb-4" style="background:var(--paper); border:1px solid var(--brass);">
                Anda bawah model <strong>Sewa Kerusi</strong> — RM{{ number_format($barber->rental_amount, 2) }} /
                {{ \App\Models\Barber::PERIOD_LABELS[$barber->rental_period] ?? $barber->rental_period }}.
                Semua hasil servis di bawah adalah milik anda sepenuhnya (tiada potongan komisen).
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card card-brand h-100" style="border-color:var(--green-ok) !important;">
                    <div class="card-body text-center p-4">
                        <div class="text-muted small mb-1">
                            @if ($barber->isChairRental())
                                Pendapatan Anda (Sewa Kerusi)
                            @else
                                Pendapatan Anda (Komisen {{ rtrim(rtrim(number_format($commissionPercent, 2), '0'), '.') }}%)
                            @endif
                        </div>
                        <div class="font-display fs-2">RM{{ number_format($totalIncome, 2) }}</div>
                        <div class="text-muted small">
                            {{ $label }}
                            @unless ($barber->isChairRental())
                                · dari jumlah harga servis RM{{ number_format($totalRevenue, 2) }}
                            @endunless
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-brand h-100">
                    <div class="card-body text-center p-4">
                        <div class="text-muted small mb-1">Jumlah Pelanggan</div>
                        <div class="font-display fs-2">{{ $totalCustomers }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Senarai tiket --}}
        <div class="card card-brand">
            <div class="card-header py-3">Butiran — {{ $label }}</div>
            <div class="card-body p-0">
                <div class="table-responsive">
<table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Tiket</th>
                            <th>Servis</th>
                            <th>Harga</th>
                            <th>{{ $barber->isChairRental() ? 'Milik Anda' : 'Komisen Anda' }}</th>
                            <th>Masa Siap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $ticket)
                            <tr>
                                <td>#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $ticket->service->name }}</td>
                                <td>RM{{ number_format($ticket->service->price, 2) }}</td>
                                <td class="fw-semibold">
                                    RM{{ number_format($barber->isChairRental() ? $ticket->service->price : $ticket->service->price * ($commissionPercent / 100), 2) }}
                                </td>
                                <td class="small text-muted">{{ $ticket->completed_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Tiada pelanggan dilayan untuk tempoh ni.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
