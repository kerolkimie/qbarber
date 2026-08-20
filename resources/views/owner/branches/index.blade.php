@extends('layouts.site')

@section('title', 'Cawangan Saya')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 mt-3">
    <h4 class="font-display mb-0">Cawangan Saya</h4>
    <a href="{{ route('owner.branches.create') }}" class="btn btn-brand btn-sm">+ Tambah Cawangan</a>
</div>

<div class="row g-4">
    @forelse ($branches as $branch)
        <div class="col-md-6">
            <div class="card card-brand h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="font-display mb-0">{{ $branch->name }}</h5>
                        <span class="badge {{ $branch->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }} text-capitalize">
                            {{ $branch->status }}
                        </span>
                    </div>
                    <p class="text-muted small mb-1">{{ $branch->address ?: 'Alamat belum diisi' }}</p>
                    <p class="text-muted small mb-3">{{ $branch->phone ?: '—' }}</p>

                    <div class="d-flex gap-3 mb-3 small">
                        <span>👤 {{ $branch->barbers_count }} tukang gunting</span>
                        <span>✂️ {{ $branch->services_count }} servis</span>
                    </div>

                    <a href="{{ route('owner.branches.qr', $branch) }}" class="btn btn-brand btn-sm w-100 mb-3">
                        📱 Lihat &amp; Cetak QR Code
                    </a>

                    <div class="d-flex gap-2">
                        <a href="{{ route('owner.branches.barbers.index', $branch) }}" class="btn btn-pine btn-sm flex-grow-1">Tukang Gunting</a>
                        <a href="{{ route('owner.branches.services.index', $branch) }}" class="btn btn-pine btn-sm flex-grow-1">Servis</a>
                        <a href="{{ route('owner.branches.edit', $branch) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card card-brand">
                <div class="card-body text-center text-muted py-5">
                    Belum ada cawangan lagi. Klik "+ Tambah Cawangan" untuk mula.
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
