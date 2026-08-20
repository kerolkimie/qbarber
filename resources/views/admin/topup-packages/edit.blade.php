@extends('layouts.site')

@section('title', 'Edit Pakej Topup')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Edit Pakej Topup</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.topup-packages.update', $package) }}">
                    @csrf
                    @method('PUT')

                    <label class="form-label fw-semibold">Bilangan Point</label>
                    <input type="number" name="points" value="{{ old('points', $package->points) }}" class="form-control mb-3" min="1" required>

                    <label class="form-label fw-semibold">Harga (RM)</label>
                    <input type="number" name="price" value="{{ old('price', $package->price) }}" class="form-control mb-3" step="0.01" min="0" required>

                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select mb-4">
                        <option value="active" {{ $package->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $package->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>

                    <button type="submit" class="btn btn-brand w-100 py-2">Kemaskini Pakej</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
