@extends('layouts.site')

@section('title', 'Lupa Kata Laluan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Lupa Kata Laluan</div>
            <div class="card-body p-4">

                <p class="text-muted small mb-4">
                    Masukkan emel yang didaftarkan. Kami akan hantar pautan untuk set semula kata laluan.
                </p>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <label for="email" class="form-label fw-semibold">Emel</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control mb-4" required autofocus>

                    <button type="submit" class="btn btn-brand w-100 py-2">Hantar Pautan Set Semula</button>

                    <p class="text-center small text-muted mt-3 mb-0">
                        <a href="{{ route('login') }}" class="text-decoration-none">Kembali ke Log Masuk</a>
                    </p>
                </form>

                @if (session('unverified_email'))
                    <hr class="my-3">
                    <p class="text-center small text-muted mb-2">Nak aktifkan akaun dahulu?</p>
                    <form method="POST" action="{{ route('verification.resend.guest') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                        <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                            Hantar Semula Emel Pengaktifan
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
