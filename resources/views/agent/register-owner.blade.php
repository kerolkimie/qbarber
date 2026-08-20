@extends('layouts.site')

@section('title', 'Daftar Barbershop')

@section('navbar')
    <x-agent-navbar />
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Daftar Barbershop Baru</div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Barbershop ni akan terus didaftarkan bawah akaun anda — tak perlu masukkan
                    kod referral (sistem auto-kaitkan sebab anda sendiri yang daftar).
                </p>

                <form method="POST" action="{{ route('agent.register-owner.store') }}">
                    @csrf

                    <label class="form-label fw-semibold">Nama Perniagaan</label>
                    <input type="text" name="business_name" value="{{ old('business_name') }}"
                           class="form-control mb-3" placeholder="cth: Blade & Fade Barbershop" required>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Owner</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control mb-3" required>
                        </div>
                        <div class="col-md-6">
                            <x-phone-field name="phone" label="No. Telefon Owner" />
                        </div>
                    </div>

                    <label class="form-label fw-semibold">Emel Owner (untuk login)</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control mb-3" required>

                    <div class="row">
                        <div class="col-md-6">
                            <x-password-field name="password" label="Kata Laluan"
                                hint="Min 8 aksara, 1 huruf besar & 1 simbol." />
                        </div>
                        <div class="col-md-6">
                            <x-password-field name="password_confirmation" label="Sahkan Kata Laluan" />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2 mt-2">Daftar Barbershop</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
