@extends('layouts.site')

@section('title', 'Set Semula Kata Laluan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card card-brand mt-4">
            <div class="card-header py-3">Set Semula Kata Laluan</div>
            <div class="card-body p-4">

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <label for="email" class="form-label fw-semibold">Emel</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                           class="form-control mb-3" required autofocus>

                    <x-password-field name="password" label="Kata Laluan Baru"
                        hint="Min 8 aksara, 1 huruf besar & 1 simbol (cth: !@#$)." />

                    <x-password-field name="password_confirmation" label="Sahkan Kata Laluan" />

                    <button type="submit" class="btn btn-brand w-100 py-2">Set Semula Kata Laluan</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection
