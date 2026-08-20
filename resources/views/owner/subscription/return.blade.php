@extends('layouts.site')

@section('title', 'Status Pembayaran')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-body p-5 text-center">
                @if ($statusId === '1')
                    <div class="mb-3" style="font-size:3rem;">✅</div>
                @elseif ($statusId === '3')
                    <div class="mb-3" style="font-size:3rem;">❌</div>
                @else
                    <div class="mb-3" style="font-size:3rem;">⏳</div>
                @endif

                <h5 class="font-display mb-3">{{ $message }}</h5>

                <a href="{{ route('owner.dashboard') }}" class="btn btn-brand">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh dashboard bila balik (bagi masa callback selesai proses).
    setTimeout(() => window.location.href = "{{ route('owner.dashboard') }}", 6000);
</script>
@endpush
