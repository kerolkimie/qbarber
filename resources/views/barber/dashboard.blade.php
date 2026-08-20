@extends('layouts.site')

@section('title', 'Dashboard Tukang Gunting')

@section('navbar')
<nav class="navbar navbar-brand-bar navbar-dark py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('barber.dashboard') }}">
            <span class="pole-dot"></span> Blade &amp; Fade
        </a>
        <div class="d-flex align-items-center">
            <a href="{{ route('profile.edit') }}" class="text-light small me-3 opacity-75 text-decoration-none d-none d-sm-inline">Akaun Saya</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm">Log Keluar</button>
            </form>
        </div>
    </div>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
            <h4 class="font-display mb-0">Hai, {{ $barber->name }}</h4>
            <span class="badge {{ $barber->current_state === 'available' ? 'text-bg-success' : ($barber->current_state === 'busy' ? 'text-bg-danger' : 'text-bg-secondary') }} text-capitalize">
                {{ $barber->current_state }}
            </span>
        </div>

        {{-- Kad Shift: Mula / Tamat Tugasan --}}
        <div class="card card-brand mb-4">
            <div class="card-header py-3">Tugasan Hari Ini</div>
            <div class="card-body p-4">
                @if ($todayShift && $todayShift->clock_in && ! $todayShift->clock_out)
                    <p class="mb-3">
                        Mula tugasan jam <strong>{{ $todayShift->clock_in->format('h:i A') }}</strong>.
                        Anda sedang <strong class="text-capitalize">{{ $barber->current_state }}</strong>.
                    </p>
                    <form method="POST" action="{{ route('barber.shift.end') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 py-2">Tamat Tugasan</button>
                    </form>

                    @if ($assignedTicket || $currentTicket)
                        <form method="POST" action="{{ route('barber.shift.end') }}" class="mt-2"
                              onsubmit="return confirm('Tamatkan tugasan serta-merta? Tiket yang belum selesai akan dikembalikan ke giliran untuk tukang gunting lain teruskan.');">
                            @csrf
                            <input type="hidden" name="force" value="1">
                            <button type="submit" class="btn btn-sm btn-link text-danger w-100 text-decoration-none">
                                ⚠ Ada hal kecemasan — Tamat Tugasan Serta-merta
                            </button>
                        </form>
                    @endif
                @else
                    @if ($todayShift && $todayShift->clock_out)
                        <p class="text-muted mb-3">
                            Anda tamat tugasan jam {{ $todayShift->clock_out->format('h:i A') }} tadi.
                            Boleh sambung semula bila-bila masa.
                        </p>
                    @else
                        <p class="text-muted mb-3">Anda belum mula tugasan hari ini.</p>
                    @endif
                    <form method="POST" action="{{ route('barber.shift.start') }}">
                        @csrf
                        <button type="submit" class="btn btn-brand w-100 py-2">Mula Tugasan</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Statistik --}}
        <div class="row g-3 mb-2">
            <div class="col-4">
                <x-stat-tile icon="bi-calendar-day" color="blue" label="Hari Ini" value="{{ $stats['today'] }}" sublabel="pelanggan" href="{{ route('barber.earnings', ['period' => 'day']) }}" />
            </div>
            <div class="col-4">
                <x-stat-tile icon="bi-calendar-week" color="gold" label="Minggu Ini" value="{{ $stats['week'] }}" sublabel="pelanggan" href="{{ route('barber.earnings', ['period' => 'week']) }}" />
            </div>
            <div class="col-4">
                <x-stat-tile icon="bi-calendar-month" color="green" label="Bulan Ini" value="{{ $stats['month'] }}" sublabel="pelanggan" href="{{ route('barber.earnings', ['period' => 'month']) }}" />
            </div>
        </div>
        <p class="text-muted small text-center mb-4">Klik kad di atas untuk lihat pendapatan terperinci</p>

        @if ($assignedTicket)
            <div class="card card-brand mb-4">
                <div class="card-header py-3">Tiket Dipanggil — Sedia Untuk Mula</div>
                <div class="card-body p-4">
                    <p class="font-display fs-4 mb-1">#{{ str_pad($assignedTicket->ticket_number, 3, '0', STR_PAD_LEFT) }}</p>
                    <p class="mb-1"><strong>Servis:</strong> {{ $assignedTicket->service->name }}</p>
                    <p class="mb-3 text-muted">{{ $assignedTicket->queueGroup->customer_name ?? 'Pelanggan' }}</p>

                    <form method="POST" action="{{ route('barber.ticket.start', $assignedTicket) }}">
                        @csrf
                        <button type="submit" class="btn btn-brand w-100 py-2">Start — Mulakan Servis</button>
                    </form>

                    <form method="POST" action="{{ route('barber.ticket.skip', $assignedTicket) }}" class="mt-2"
                          onsubmit="return confirm('Langkau tiket #{{ $assignedTicket->ticket_number }}? Pelanggan akan ditandakan tidak hadir.');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">⏭ Skip — Pelanggan Tidak Hadir</button>
                    </form>
                </div>
            </div>
        @elseif ($currentTicket)
            <div class="card card-brand mb-4">
                <div class="card-header py-3">Sedang Dilayan</div>
                <div class="card-body p-4">
                    <p class="font-display fs-4 mb-1">#{{ str_pad($currentTicket->ticket_number, 3, '0', STR_PAD_LEFT) }}</p>
                    <p class="mb-1"><strong>Servis:</strong> {{ $currentTicket->service->name }}</p>
                    <p class="mb-3 text-muted">{{ $currentTicket->queueGroup->customer_name ?? 'Pelanggan' }}</p>

                    <form method="POST" action="{{ route('barber.ticket.next', $currentTicket) }}">
                        @csrf
                        <button type="submit" class="btn btn-finish w-100 py-2">Next — Selesai</button>
                    </form>
                </div>
            </div>
        @elseif ($barber->current_state === 'available')
            <div class="card card-brand mb-4">
                <div class="card-header py-3">Sedia Untuk Pelanggan?</div>
                <div class="card-body p-4 text-center">
                    @if ($waitingCount > 0)
                        <p class="text-muted mb-3">{{ $waitingCount }} pelanggan menunggu di cawangan.</p>
                        <form method="POST" action="{{ route('barber.call.next') }}">
                            @csrf
                            <button type="submit" class="btn btn-brand w-100 py-2">📣 Panggil Pelanggan Seterusnya</button>
                        </form>
                    @else
                        <p class="text-muted mb-0">Tiada pelanggan menunggu buat masa ini.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="card card-brand mb-4">
                <div class="card-header py-3">Tiket Anda</div>
                <div class="card-body p-4 text-center text-muted">
                    Tekan "Mula Tugasan" di atas untuk mula terima pelanggan.
                </div>
            </div>
        @endif

        <div class="card card-brand mb-4">
            <div class="card-body d-flex justify-content-between align-items-center p-4">
                <span class="text-muted">Bilangan pelanggan menunggu di cawangan</span>
                <span class="font-display fs-4">{{ $waitingCount }}</span>
            </div>
        </div>

        {{-- QR Cawangan Sendiri — tunjuk terus skrin ni kepada pelanggan kalau poster
             di kaunter hilang/koyak atau kedai padat, pelanggan boleh scan terus. --}}
        <div class="card card-brand">
            <div class="card-header py-3">QR Cawangan Saya</div>
            <div class="card-body p-4 text-center">
                <p class="text-muted small mb-3">
                    Poster QR hilang atau kedai padat? Tunjukkan skrin ni kepada pelanggan untuk mereka scan terus.
                </p>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode(url('/q/' . $barber->branch->qr_token)) }}"
                     alt="QR {{ $barber->branch->name }}" class="mb-2" style="border-radius:10px; border:8px solid var(--paper);">
                <p class="fw-semibold mb-0">{{ $barber->branch->name }}</p>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh dashboard setiap 8 saat supaya barber nampak tiket baru auto-assign
    setTimeout(() => window.location.reload(), 8000);
</script>
@endpush
