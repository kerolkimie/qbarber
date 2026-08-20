@extends('layouts.site')

@section('title', $branch->name . ' — Skrin Giliran')

@section('content')
<div class="text-center mb-4">
    <h2 class="font-display">{{ $branch->name }}</h2>
    <p class="text-muted">Skrin Giliran Semasa</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="serving-panel h-100">
            <p class="eyebrow mb-2">Sedang Dilayan</p>
            @forelse ($serving as $ticket)
                @php $isMine = in_array($ticket->id, $myTicketIds); @endphp
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary-subtle py-2 {{ $isMine ? 'rounded px-2' : '' }}"
                     style="{{ $isMine ? 'background:rgba(220,184,100,0.25); border-left:4px solid var(--brass);' : '' }}">
                    <div>
                        <span class="badge badge-ticket fs-6">#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</span>
                        <span class="ms-2">{{ $ticket->service->name }}</span>
                        @if ($isMine)
                            <span class="badge ms-1" style="background:var(--brass); color:var(--pine-deep);">Tiket Anda</span>
                        @endif
                    </div>
                    <span class="small">{{ $ticket->barber->name ?? '-' }}</span>
                </div>
            @empty
                <p class="fst-italic mb-0" style="color:var(--brass-soft);">Tiada pelanggan sedang dilayan</p>
            @endforelse
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-brand h-100">
            <div class="card-header py-3">Senarai Menunggu</div>
            <div class="card-body">
                @forelse ($waiting as $ticket)
                    @php $isMine = in_array($ticket->id, $myTicketIds); @endphp
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2 {{ $isMine ? 'rounded px-2' : '' }}"
                         style="{{ $isMine ? 'background:rgba(220,184,100,0.25); border-left:4px solid var(--brass);' : '' }}">
                        <div>
                            <span class="badge badge-ticket">#{{ str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) }}</span>
                            <span class="ms-2">{{ $ticket->service->name }}</span>
                            @if ($isMine)
                                <span class="badge ms-1" style="background:var(--brass); color:var(--pine-deep);">Tiket Anda</span>
                            @endif
                        </div>
                        <span class="text-muted small">~{{ $ticket->live_estimate }} min</span>
                    </div>
                @empty
                    <p class="text-muted fst-italic mb-0">Tiada sesiapa menunggu</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<p class="text-center text-muted small mt-4">Skrin ini auto-refresh setiap 10 saat.</p>

@if ($myTickets->isNotEmpty())
    {{-- Spacer supaya widget terapung di bawah tak tutup content --}}
    <div style="height:70px;"></div>

    <div style="position:fixed; bottom:0; left:0; right:0; background:var(--pine); color:var(--paper); padding:14px 16px; text-align:center; z-index:1000; box-shadow:0 -4px 14px rgba(0,0,0,0.2); overflow-x:auto; white-space:nowrap;">
        <span class="font-display" style="letter-spacing:0.5px;">🎫 Tiket Anda:</span>
        @foreach ($myTickets as $t)
            <span class="badge badge-ticket mx-1">
                #{{ str_pad($t->ticket_number, 3, '0', STR_PAD_LEFT) }}
                — {{ $t->status === 'in_progress' ? 'Sedang Dilayan' : 'Menunggu' }}
            </span>
        @endforeach
    </div>
@endif
@endsection

@push('scripts')
<script>
    setTimeout(() => window.location.reload(), 10000);
</script>
@endpush
