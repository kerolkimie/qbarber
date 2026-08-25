@extends('layouts.site')

@section('title', 'Log Masuk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">

        @if (request('registered'))
            <div class="alert alert-success mt-4 mb-0">
                Pendaftaran berjaya! Emel pengaktifan telah dihantar — sila rujuk emel yang didaftarkan
                (<strong>{{ request('email') }}</strong>) dan klik pautan pengaktifan sebelum log masuk.
            </div>
        @elseif (request('email_updated'))
            <div class="alert alert-success mt-4 mb-0">
                Emel akaun anda berjaya dikemaskini kepada <strong>{{ request('email') }}</strong>.
                Sila semak emel tersebut untuk pautan pengesahan sebelum log masuk semula.
            </div>
        @endif

        <div class="card card-brand mt-4">
            <div class="card-header py-3">Log Masuk</div>
            <div class="card-body p-4">

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <label for="email" class="form-label fw-semibold">Emel</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control mb-3" required autofocus autocomplete="username">

                    <x-password-field name="password" label="Kata Laluan" autocomplete="current-password" />

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small" for="remember">Ingat saya</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                Lupa kata laluan?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2">Log Masuk</button>

                    @if (Route::has('register'))
                        <p class="text-center small text-muted mt-3 mb-0">
                            Belum ada akaun?
                            <a href="{{ route('register') }}" class="text-decoration-none">Daftar Barbershop</a>
                        </p>
                    @endif
                </form>

                @if (session('unverified_email'))
                    <hr class="my-3">
                    <p class="text-center small text-muted mb-2">Tak terima emel pengaktifan?</p>
                    <form method="POST" action="{{ route('verification.resend.guest') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                            Hantar Semula ke {{ session('unverified_email') }}
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
