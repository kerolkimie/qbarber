@extends('layouts.site')

@section('title', 'Tambah Pakej')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Tambah Pakej Baru</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.plans.store') }}">
                    @csrf

                    <label class="form-label fw-semibold">Nama Pakej</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control mb-3" required>

                    <label class="form-label fw-semibold">Harga (RM/bulan)</label>
                    <input type="number" name="price" value="{{ old('price') }}" class="form-control mb-3" step="0.01" min="0" required>

                    <label class="form-label fw-semibold">Tempoh (hari)</label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', 30) }}" class="form-control mb-3" min="1" required>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Had Cawangan</label>
                            <input type="number" name="max_branches" value="{{ old('max_branches', 1) }}" class="form-control mb-3" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Had Kerusi (Tukang Gunting)</label>
                            <input type="number" name="max_barbers" value="{{ old('max_barbers', 5) }}" class="form-control mb-3" min="1" required>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" name="is_per_branch_limit" value="1" class="form-check-input" id="perBranch" {{ old('is_per_branch_limit') ? 'checked' : '' }}>
                        <label class="form-check-label" for="perBranch">
                            Had kerusi terpakai <strong>SETIAP cawangan</strong> (bukan jumlah keseluruhan)
                        </label>
                        <p class="text-muted small mb-0">Cth: Premium 5 kerusi/cawangan × 3 cawangan = 15 kerusi keseluruhan. Kalau tak ditanda, had ni jadi jumlah TOTAL merentasi semua cawangan.</p>
                    </div>

                    <label class="form-label fw-semibold">Ciri-ciri (satu baris = satu ciri)</label>
                    <textarea name="features" rows="4" class="form-control mb-4" placeholder="cth: QR code tanpa had">{{ old('features') }}</textarea>

                    <button type="submit" class="btn btn-brand w-100 py-2">Simpan Pakej</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
