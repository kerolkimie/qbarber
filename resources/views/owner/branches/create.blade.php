@extends('layouts.site')

@section('title', 'Tambah Cawangan')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Tambah Cawangan Baru</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('owner.branches.store') }}">
                    @csrf

                    <label class="form-label fw-semibold">Nama Cawangan</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control mb-3" placeholder="cth: Blade & Fade — Cawangan Cheras" required>

                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea name="address" class="form-control mb-3" rows="2">{{ old('address') }}</textarea>

                    <x-phone-field name="phone" label="No. Telefon Cawangan" />

                    <label class="form-label fw-semibold">Peratus Komisen Tukang Gunting (%)</label>
                    <input type="number" name="commission_percent" value="{{ old('commission_percent', 40) }}"
                           class="form-control mb-1" step="0.01" min="0" max="100" required>
                    <p class="text-muted small mb-3">Cth: 40 bermaksud tukang gunting dapat 40% dari harga setiap servis yang dia siapkan.</p>

                    <button type="submit" class="btn btn-brand w-100 py-2 mt-2">Simpan Cawangan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
