@extends('layouts.site')

@section('title', 'Kemaskini Akaun')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">

        <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
            <h4 class="font-display mb-0">Kemaskini Akaun</h4>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">&larr; Kembali ke Dashboard</a>
        </div>

        {{-- Maklumat Profil --}}
        <div class="card card-brand mb-4">
            <div class="card-header py-3">Maklumat Profil</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.update') }}" id="profile-form">
                    @csrf
                    @method('PATCH')

                    <label class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control mb-3" required>

                    <label class="form-label fw-semibold">Emel</label>
                    <input type="email" name="email" id="email-input" data-original-email="{{ $user->email }}"
                           value="{{ old('email', $user->email) }}" class="form-control mb-1" required>
                    @if (! $user->hasVerifiedEmail())
                        <p class="text-warning small mb-3">⚠ Emel ini belum disahkan.</p>
                    @else
                        <p class="text-muted small mb-3">Tukar emel akan perlukan pengesahan semula — anda akan log keluar automatik selepas simpan.</p>
                    @endif

                    <x-phone-field name="phone" label="No. Telefon" :value="$user->phone" />

                    <button type="submit" class="btn btn-brand w-100 py-2 mt-2">Simpan Perubahan</button>
                </form>
            </div>
        </div>

        @push('scripts')
        <script>
            document.getElementById('profile-form').addEventListener('submit', function (e) {
                const emailInput = document.getElementById('email-input');
                const originalEmail = emailInput.getAttribute('data-original-email');
                const newEmail = emailInput.value.trim();

                if (newEmail !== originalEmail) {
                    const confirmed = confirm(
                        'Anda menukar emel dari "' + originalEmail + '" kepada "' + newEmail + '".\n\n' +
                        'Emel pengaktifan baru akan dihantar ke alamat baru ni, dan anda akan LOG KELUAR automatik ' +
                        'sehingga emel baru disahkan.\n\nTeruskan?'
                    );
                    if (!confirmed) {
                        e.preventDefault();
                    }
                }
            });
        </script>
        @endpush

        {{-- Tukar Kata Laluan --}}
        <div class="card card-brand mb-4">
            <div class="card-header py-3">Tukar Kata Laluan</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')

                    <x-password-field name="current_password" label="Kata Laluan Semasa" autocomplete="current-password" />

                    <div class="row">
                        <div class="col-md-6">
                            <x-password-field name="password" label="Kata Laluan Baru"
                                hint="Min 8 aksara, 1 huruf besar & 1 simbol." />
                        </div>
                        <div class="col-md-6">
                            <x-password-field name="password_confirmation" label="Sahkan Kata Laluan Baru" />
                        </div>
                    </div>

                    <button type="submit" class="btn btn-pine w-100 py-2 mt-2">Tukar Kata Laluan</button>
                </form>
            </div>
        </div>

        {{-- Zon Bahaya --}}
        <div class="card card-brand" style="border-color:var(--red) !important;">
            <div class="card-header py-3" style="background:var(--red);">Padam Akaun</div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Tindakan ini kekal dan tidak boleh diundur. Semua data berkaitan akaun anda akan dipadam.
                </p>
                <form method="POST" action="{{ route('profile.destroy') }}"
                      onsubmit="return confirm('Padam akaun anda secara kekal? Tindakan ini TIDAK boleh diundur.');">
                    @csrf
                    @method('DELETE')

                    <label class="form-label fw-semibold">Sahkan dengan Kata Laluan</label>
                    <input type="password" name="password" class="form-control mb-3" required>

                    <button type="submit" class="btn btn-outline-danger w-100 py-2">Padam Akaun Saya</button>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
