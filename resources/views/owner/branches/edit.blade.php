@extends('layouts.site')

@section('title', 'Edit Cawangan')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Edit Cawangan — {{ $branch->name }}</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('owner.branches.update', $branch) }}">
                    @csrf
                    @method('PUT')

                    <label class="form-label fw-semibold">Nama Cawangan</label>
                    <input type="text" name="name" value="{{ old('name', $branch->name) }}"
                           class="form-control mb-3" required>

                    <label class="form-label fw-semibold">Alamat</label>
                    <textarea name="address" class="form-control mb-3" rows="2">{{ old('address', $branch->address) }}</textarea>

                    <x-phone-field name="phone" label="No. Telefon Cawangan" :value="$branch->phone" />

                    <label class="form-label fw-semibold">Peratus Komisen Tukang Gunting (%)</label>
                    <input type="number" name="commission_percent" value="{{ old('commission_percent', $branch->commission_percent) }}"
                           class="form-control mb-1" step="0.01" min="0" max="100" required>
                    <p class="text-muted small mb-3">Cth: 40 bermaksud tukang gunting dapat 40% dari harga setiap servis yang dia siapkan.</p>

                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select mb-4">
                        <option value="active" {{ $branch->status === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ $branch->status === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>

                    <button type="submit" class="btn btn-brand w-100 py-2">Kemaskini Cawangan</button>
                </form>

                <form method="POST" action="{{ route('owner.branches.destroy', $branch) }}" class="mt-3"
                      onsubmit="return confirm('Padam cawangan ini? Tindakan ini tidak boleh diundur.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">Padam Cawangan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
