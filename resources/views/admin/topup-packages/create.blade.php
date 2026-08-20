@extends('layouts.site')

@section('title', 'Tambah Pakej Topup')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Tambah Pakej Topup</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.topup-packages.store') }}">
                    @csrf

                    <label class="form-label fw-semibold">Bilangan Point</label>
                    <input type="number" name="points" value="{{ old('points') }}" class="form-control mb-3" min="1" required>

                    <label class="form-label fw-semibold">Harga (RM)</label>
                    <input type="number" name="price" value="{{ old('price') }}" class="form-control mb-4" step="0.01" min="0" required>

                    <button type="submit" class="btn btn-brand w-100 py-2">Simpan Pakej</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
