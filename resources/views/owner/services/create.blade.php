@extends('layouts.site')

@section('title', 'Tambah Servis')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Tambah Servis — {{ $branch->name }}</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('owner.branches.services.store', $branch) }}">
                    @csrf

                    <label class="form-label fw-semibold">Nama Servis</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control mb-3" placeholder="cth: Haircut, Beard Trim" required>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga (RM)</label>
                            <input type="number" name="price" value="{{ old('price') }}"
                                   class="form-control mb-3" step="0.01" min="0" placeholder="cth: 25.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempoh (minit)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}"
                                   class="form-control mb-3" min="5" placeholder="cth: 20" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 mt-2">Simpan Servis</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
