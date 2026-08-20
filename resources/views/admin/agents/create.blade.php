@extends('layouts.site')

@section('title', 'Daftar Ejen')

@section('navbar')
    <x-admin-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Daftar Ejen Baru</div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Akaun login akan terus dicipta untuk ejen ni. Kod referral unik akan dijana
                    automatik — ejen boleh kongsi kod ni kepada bakal owner semasa daftar di /daftar.
                </p>

                <form method="POST" action="{{ route('admin.agents.store') }}">
                    @csrf

                    <label class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control mb-3" required>

                    <label class="form-label fw-semibold">Emel (untuk login)</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control mb-3" required>

                    <x-phone-field name="phone" label="No. Telefon" />

                    <label class="form-label fw-semibold">Peratus Komisen (%)</label>
                    <input type="number" name="commission_percent" value="{{ old('commission_percent', 10) }}"
                           class="form-control mb-1" step="0.01" min="0" max="100" required>
                    <p class="text-muted small mb-3">
                        Peratus dari nilai subscription yang dibayar owner yang didaftar melalui ejen ni.
                    </p>

                    <div class="row">
                        <div class="col-md-6">
                            <x-password-field name="password" label="Kata Laluan"
                                hint="Min 8 aksara, 1 huruf besar & 1 simbol." />
                        </div>
                        <div class="col-md-6">
                            <x-password-field name="password_confirmation" label="Sahkan Kata Laluan" />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 mt-2">Daftar Ejen</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
