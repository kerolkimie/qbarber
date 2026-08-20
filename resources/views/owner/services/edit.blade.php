@extends('layouts.site')

@section('title', 'Edit Servis')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Edit Servis — {{ $service->name }}</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('owner.services.update', $service) }}">
                    @csrf
                    @method('PUT')

                    <label class="form-label fw-semibold">Nama Servis</label>
                    <input type="text" name="name" value="{{ old('name', $service->name) }}"
                           class="form-control mb-3" required>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga (RM)</label>
                            <input type="number" name="price" value="{{ old('price', $service->price) }}"
                                   class="form-control mb-3" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tempoh (minit)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}"
                                   class="form-control mb-3" min="5" required>
                        </div>
                    </div>

                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select mb-4">
                        <option value="active" {{ $service->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $service->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>

                    <button type="submit" class="btn btn-brand w-100 py-2">Kemaskini Servis</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
