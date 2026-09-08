@extends('layouts.site')

@section('title', 'Tetapan Sistem')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <h4 class="font-display mb-4 mt-3">Tetapan Sistem</h4>

        <div class="card card-brand">
            <div class="card-header py-3">Hubungi Kami / WhatsApp</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf

                    <label class="form-label fw-semibold">No. Telefon WhatsApp</label>
                    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $whatsappNumber) }}"
                           class="form-control mb-1" placeholder="cth: 013-4558430">
                    <p class="text-muted small mb-3">
                        Format bebas (boleh guna sengkang/space) — sistem akan tukar automatik jadi pautan
                        WhatsApp Web bila pelanggan klik butang WhatsApp di landing page.
                    </p>

                    <button type="submit" class="btn btn-brand">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
