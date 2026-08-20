@extends('layouts.site')

@section('title', 'Tiket Anda')

@section('content')
<div class="text-center mb-4">
    <p class="font-display text-muted mb-1">{{ $branch->name }}</p>
    <h4 class="font-display">Tiket Anda Sudah Sedia</h4>
</div>

<div class="row justify-content-center g-4">
    @foreach ($tickets as $ticket)
        <div class="col-md-5">
            <div class="ticket-card">
                <p class="eyebrow mb-0">Nombor Giliran</p>
                <div class="num">#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</div>
                <p class="font-display mb-1">{{ $ticket->service->name }}</p>
                <p class="text-muted small mb-3">Anggaran ~{{ $ticket->live_estimate }} minit</p>
                <div class="border rounded p-2 bg-white small">
                    Status: <strong class="text-capitalize">{{ str_replace('_', ' ', $ticket->status) }}</strong>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="text-center mt-4">
    <a href="{{ route('queue.display', $branch->qr_token) }}" class="btn btn-pine">Lihat Skrin Giliran Semasa</a>
    <a href="{{ route('queue.show', $branch->qr_token) }}" class="btn btn-outline-secondary">Ambil Tiket Lain</a>
</div>

@php
    $ticketNumbers = collect($tickets)->pluck('ticket_number')
        ->map(fn ($n) => '#' . str_pad($n, 3, '0', STR_PAD_LEFT))
        ->implode(', ');

    $waMessage = "Tiket giliran saya di {$branch->name}: {$ticketNumbers}. "
        . "Semak giliran semasa di: " . route('queue.display', $branch->qr_token);
@endphp

<div class="text-center mt-3">
    <a href="https://wa.me/?text={{ urlencode($waMessage) }}" target="_blank" rel="noopener"
       class="btn btn-outline-success btn-sm">
        <i class="bi bi-whatsapp"></i> Simpan Nombor Tiket ke WhatsApp
    </a>
    <p class="text-muted small mt-2 mb-0">Elak lupa nombor giliran anda — hantar kat diri sendiri atau kawan.</p>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh setiap 15 saat supaya anggaran masa & status tiket sentiasa terkini.
    setTimeout(() => window.location.reload(), 15000);
</script>
@endpush
