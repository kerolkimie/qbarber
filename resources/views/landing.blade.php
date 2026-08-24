@extends('layouts.site')

@section('title', 'Blade & Fade — Sistem Giliran Barbershop')

@section('navbar')
<nav class="navbar navbar-brand-bar navbar-dark py-3">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
            <span class="pole-dot"></span> Blade &amp; Fade
        </a>
        <div>
            <a href="#harga" class="btn btn-outline-light btn-sm me-2">Pakej</a>
            <a href="#hubungi" class="btn btn-outline-light btn-sm me-2">Hubungi</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Log Masuk</a>
            <a href="{{ route('register.owner') }}" class="btn btn-brand btn-sm">Daftar Barbershop</a>
        </div>
    </div>
</nav>
@endsection

@section('content')

{{-- ============ HERO ============ --}}
<div class="hero-landing rounded-4 p-5 mb-5 position-relative">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background:rgba(231,199,122,.16); color:var(--brass-soft); font-family:'JetBrains Mono',monospace; letter-spacing:1px; font-size:.72rem;">
                ⚡ GILIRAN DIGITAL · TANPA APLIKASI DIMUAT TURUN
            </span>
            <h1 class="font-display display-4 mb-3" style="line-height:1.08;">Kedai Anda.<br><span class="brass-text">Giliran Yang Tersusun.</span></h1>
            <p class="mb-4 opacity-75 fs-5" style="max-width:520px;">
                Pelanggan imbas QR, ambil nombor, dan tunggu di mana sahaja. Tukang gunting urus giliran
                dengan satu ketikan sahaja — tiada lagi kertas, tiada lagi sesak di kaunter.
            </p>
            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('register.owner') }}" class="btn btn-brand btn-lg px-4">Mulakan Percuma</a>
                <a href="#macam-mana" class="btn btn-outline-light btn-lg px-4">Lihat Cara Ia Berfungsi</a>
            </div>
        </div>
        <div class="col-lg-5 d-none d-lg-flex justify-content-center">
            <div class="ticket-card mt-4" style="transform:rotate(-4deg);">
                <p class="eyebrow mb-0">Tiket Anda</p>
                <div class="num">#014</div>
                <p class="font-display mb-1">Haircut + Beard</p>
                <p class="text-muted small mb-3">Anggaran ~18 minit</p>
                <div class="border rounded p-2 bg-white small">Status: <strong>Menunggu</strong></div>
            </div>
        </div>
    </div>
</div>

{{-- ============ CARA IA BERFUNGSI ============ --}}
<div id="macam-mana" class="mb-5 pt-3">
    <div class="text-center mb-5">
        <p class="text-muted font-display small mb-2" style="letter-spacing:2px;">CARA IA BERFUNGSI</p>
        <h2 class="font-display">Dari Imbasan ke Gunting — Tiga Langkah</h2>
    </div>
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="card card-brand h-100">
                <div class="card-body p-4">
                    <div class="feature-icon mx-auto"><i class="bi bi-qr-code-scan"></i></div>
                    <h5 class="font-display">Imbas &amp; Pilih Servis</h5>
                    <p class="text-muted small mb-0">Pelanggan imbas QR di kaunter, masukkan bilangan orang &amp; pilih servis untuk setiap seorang.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-brand h-100">
                <div class="card-body p-4">
                    <div class="feature-icon mx-auto"><i class="bi bi-ticket-perforated"></i></div>
                    <h5 class="font-display">Dapat Nombor Giliran</h5>
                    <p class="text-muted small mb-0">Tiket digital terus dijana dengan anggaran masa menunggu yang dikira secara langsung.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-brand h-100">
                <div class="card-body p-4">
                    <div class="feature-icon mx-auto"><i class="bi bi-scissors"></i></div>
                    <h5 class="font-display">Tukang Gunting Urus Sendiri</h5>
                    <p class="text-muted small mb-0">Panggil pelanggan seterusnya bila betul-betul ready — giliran kekal adil dan telus.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ PENGURUSAN PENUH ============ --}}
<div class="row g-4 mb-5 align-items-center">
    <div class="col-lg-6">
        <p class="text-muted font-display small mb-2" style="letter-spacing:2px;">SATU DASHBOARD, SEMUA KAWALAN</p>
        <h2 class="font-display mb-3">Bukan Setakat Giliran — Urus Seluruh Kedai</h2>
        <p class="text-muted mb-4">Cawangan, tukang gunting, servis, komisen, dan laporan pendapatan — semua dalam satu tempat, boleh diakses dari mana sahaja.</p>
        <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-start gap-3">
                <div class="feature-icon flex-shrink-0" style="margin-bottom:0;"><i class="bi bi-diagram-3"></i></div>
                <div><strong>Berbilang Cawangan</strong><p class="text-muted small mb-0">Urus semua lokasi dari satu akaun.</p></div>
            </div>
            <div class="d-flex align-items-start gap-3">
                <div class="feature-icon flex-shrink-0" style="margin-bottom:0;"><i class="bi bi-cash-coin"></i></div>
                <div><strong>Komisen Automatik</strong><p class="text-muted small mb-0">Pilih model komisen atau sewa kerusi untuk setiap tukang gunting.</p></div>
            </div>
            <div class="d-flex align-items-start gap-3">
                <div class="feature-icon flex-shrink-0" style="margin-bottom:0;"><i class="bi bi-bar-chart"></i></div>
                <div><strong>Laporan Masa Nyata</strong><p class="text-muted small mb-0">Tengok hasil, komisen, dan prestasi setiap cawangan bila-bila masa.</p></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-brand">
            <div class="card-header py-3">Ringkasan Cawangan</div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6"><x-stat-tile icon="bi-shop" color="blue" label="Cawangan" value="3" /></div>
                    <div class="col-6"><x-stat-tile icon="bi-scissors" color="gold" label="Tukang Gunting" value="12" /></div>
                    <div class="col-6"><x-stat-tile icon="bi-check-circle" color="green" label="Dilayan Hari Ini" value="47" /></div>
                    <div class="col-6"><x-stat-tile icon="bi-cash-stack" color="green" label="Hasil Hari Ini" value="RM940" /></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ HARGA ============ --}}
<div id="harga" class="mb-5 pt-3">
    <div class="text-center mb-5">
        <p class="text-muted font-display small mb-2" style="letter-spacing:2px;">PILIH PAKEJ ANDA</p>
        <h2 class="font-display">Harga Yang Sesuai Untuk Setiap Saiz Barbershop</h2>
        <p class="text-muted">Setiap pakej datang dengan peruntukan point bulanan — 1 point ditolak setiap tugasan siap.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach ($plans as $index => $plan)
            @php $popular = $index === 1; @endphp
            <div class="col-md-4">
                <div class="card card-brand pricing-card h-100 {{ $popular ? 'is-popular' : '' }}">
                    @if ($popular)
                        <div class="pricing-ribbon">Paling Popular</div>
                    @endif
                    <div class="card-body p-4 d-flex flex-column">
                        <h4 class="font-display text-center mb-1">{{ $plan->name }}</h4>
                        <p class="text-center mb-4">
                            <span class="font-display" style="font-size:2.4rem; color:var(--pine);">RM{{ number_format($plan->price, 0) }}</span>
                            <span class="text-muted small">/ {{ $plan->duration_days }} hari</span>
                        </p>

                        <ul class="list-unstyled mb-4 flex-grow-1">
                            <li class="mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-shop" style="color:var(--pine);"></i>
                                <strong>{{ $plan->max_branches }} {{ $plan->max_branches > 1 ? 'cawangan' : 'cawangan' }}</strong>
                            </li>
                            <li class="mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-scissors" style="color:var(--brass);"></i>
                                <strong>Sehingga {{ $plan->max_barbers }} kerusi{{ $plan->is_per_branch_limit ? ' / cawangan' : '' }}</strong>
                            </li>
                            @foreach (explode("\n", $plan->features) as $feature)
                                <li class="mb-2 text-muted small d-flex align-items-start gap-2">
                                    <i class="bi bi-check2" style="color:var(--green-ok); margin-top:2px;"></i>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('register.owner', ['plan' => $plan->name]) }}" class="btn {{ $popular ? 'btn-brand' : 'btn-pine' }} w-100 py-2">
                            Pilih {{ $plan->name }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ============ HUBUNGI KAMI ============ --}}
<div id="hubungi" class="mb-5 pt-3">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5">
            <p class="text-muted font-display small mb-2" style="letter-spacing:2px;">ADA SOALAN?</p>
            <h2 class="font-display mb-3">Hubungi Kami</h2>
            <p class="text-muted mb-0">
                Belum pasti pakej mana sesuai untuk kedai anda? Isikan borang ni, pasukan kami akan hubungi
                anda tak lama lagi untuk bantu pilih pakej yang paling sesuai.
            </p>
        </div>
        <div class="col-lg-7">
            <div class="card card-brand">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <x-phone-field name="phone" label="No. Telefon" :value="old('phone')" />
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Emel (optional)</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Perniagaan (optional)</label>
                                <input type="text" name="business_name" value="{{ old('business_name') }}" class="form-control">
                            </div>
                        </div>

                        <label class="form-label fw-semibold small">Mesej (optional)</label>
                        <textarea name="message" rows="3" class="form-control mb-3" placeholder="Cth: Saya ada 2 cawangan, pakej mana sesuai?">{{ old('message') }}</textarea>

                        <button type="submit" class="btn btn-brand px-4">Hantar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ CTA PENUTUP ============ --}}
<div id="daftar" class="hero-landing rounded-4 mb-5">
    <div class="card-body p-5 text-center">
        <h3 class="font-display mb-2">Sedia Untuk Mulakan?</h3>
        <p class="opacity-75 mb-4">Daftar barbershop anda dalam masa kurang 2 minit — tiada kad kredit diperlukan untuk mula.</p>
        <a href="{{ route('register.owner') }}" class="btn btn-brand btn-lg px-4">Daftar Barbershop Sekarang</a>
    </div>
</div>
@endsection
