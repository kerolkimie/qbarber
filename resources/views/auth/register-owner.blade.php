@extends('layouts.site')

@section('title', 'Daftar Barbershop')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Daftar Barbershop Anda</div>
            <div class="card-body p-4">

                @if ($selectedPlan)
                    <div class="alert" style="background:var(--paper); border:1px solid var(--brass);">
                        Pakej dipilih: <strong>{{ $selectedPlan }}</strong> — boleh disahkan selepas pendaftaran akaun.
                    </div>
                @endif

                <form method="POST" action="{{ route('register.owner.store') }}">
                    @csrf

                    <label class="form-label fw-semibold">Nama Perniagaan</label>
                    <input type="text" name="business_name" value="{{ old('business_name') }}"
                           class="form-control mb-3" placeholder="cth: Blade & Fade Barbershop" required>

                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Anda</label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control mb-3" required>
                        </div>
                        <div class="col-md-6">
                            <x-phone-field name="phone" label="No. Telefon" />
                        </div>
                    </div>

                    <label class="form-label fw-semibold">Emel</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="form-control mb-3" required>

                    <div class="row">
                        <div class="col-md-6">
                            <x-password-field name="password" label="Kata Laluan"
                                hint="Min 8 aksara, 1 huruf besar & 1 simbol (cth: !@#$)." />
                        </div>
                        <div class="col-md-6">
                            <x-password-field name="password_confirmation" label="Sahkan Kata Laluan" />
                        </div>
                    </div>

                    <label class="form-label fw-semibold">Kod Agent (jika ada)</label>
                    <input type="text" name="agent_code" value="{{ old('agent_code') }}"
                           class="form-control mb-4" placeholder="Optional — jika didaftarkan melalui agent">

                    <button type="submit" class="btn btn-brand w-100 py-2">Daftar Sekarang</button>

                    <p class="text-center small text-muted mt-3 mb-0">
                        Dah ada akaun? <a href="{{ route('login') }}" class="text-decoration-none">Log Masuk</a>
                    </p>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
