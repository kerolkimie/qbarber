@extends('layouts.site')

@section('title', 'Daftar Tukang Gunting')

@section('navbar')
    <x-owner-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Daftar Tukang Gunting — {{ $branch->name }}</div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Akaun login akan terus dicipta untuk tukang gunting ni — dia boleh log masuk terus
                    guna emel &amp; kata laluan di bawah untuk akses dashboard dia sendiri.
                </p>

                <form method="POST" action="{{ route('owner.branches.barbers.store', $branch) }}">
                    @csrf

                    <label class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control mb-3" required>

                    <label class="form-label fw-semibold">Emel (untuk login)</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control mb-3" required>

                    <x-phone-field name="phone" label="No. Telefon" />

                    <div class="row">
                        <div class="col-md-6">
                            <x-password-field name="password" label="Kata Laluan"
                                hint="Min 8 aksara, 1 huruf besar & 1 simbol." />
                        </div>
                        <div class="col-md-6">
                            <x-password-field name="password_confirmation" label="Sahkan Kata Laluan" />
                        </div>
                    </div>

                    @include('owner.barbers._payment-fields')

                    <button type="submit" class="btn btn-brand w-100 py-2 mt-2">Daftar Tukang Gunting</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
