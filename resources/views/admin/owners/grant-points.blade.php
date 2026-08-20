@extends('layouts.site')

@section('title', 'Bagi Point Manual')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<a href="{{ route('admin.owners.show', $owner) }}" class="btn btn-outline-secondary btn-sm mt-3 mb-3">&larr; Kembali</a>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand">
            <div class="card-header py-3">Bagi Point Manual — {{ $owner->business_name }}</div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Guna borang ni untuk bagi point terus kepada owner <strong>tanpa</strong> melalui proses topup/bayaran
                    (cth: sebagai kompensasi, promosi, atau bantuan khas). Awak tetapkan sendiri bila point ni luput.
                </p>

                <form method="POST" action="{{ route('admin.owners.grantPoints.store', $owner) }}">
                    @csrf

                    <label class="form-label fw-semibold">Bilangan Point</label>
                    <input type="number" name="points" value="{{ old('points') }}" class="form-control mb-3" min="1" required>

                    <label class="form-label fw-semibold">Point Boleh Digunakan Sehingga</label>
                    <input type="date" name="expires_at" value="{{ old('expires_at', today()->addMonths(3)->format('Y-m-d')) }}"
                           class="form-control mb-1" required>
                    <p class="text-muted small mb-3">Default 3 bulan dari sekarang, tapi boleh ubah ikut keperluan.</p>

                    <label class="form-label fw-semibold">Nota (optional)</label>
                    <textarea name="note" rows="2" class="form-control mb-4" placeholder="cth: Kompensasi gangguan sistem">{{ old('note') }}</textarea>

                    <button type="submit" class="btn btn-brand w-100 py-2">Bagi Point</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
