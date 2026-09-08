@extends('layouts.site')

@section('title', 'Blade & Fade — Sistem Giliran Barbershop')

@include('partials.site-photo-bg')

@push('styles')
<style>
    :root{ scroll-behavior:smooth; }

    .lp{ --lp-radius:22px; }
    .lp .lp-block{ scroll-margin-top:96px; }
    .lp .eyebrow-label{
        font-family:'JetBrains Mono',monospace; letter-spacing:2px; font-size:.72rem;
        text-transform:uppercase; color:var(--pine); font-weight:700;
    }

    /* ---------- Sticky navbar polish ---------- */
    .navbar-brand-bar.sticky-top{ transition:padding .25s ease, box-shadow .25s ease; }
    .navbar-brand-bar.is-scrolled{
        padding-top:.55rem !important; padding-bottom:.55rem !important;
        box-shadow:0 8px 28px rgba(15,32,58,.28);
    }
    .lp-navlinks{ display:flex; align-items:center; flex-wrap:wrap; gap:.4rem; }

    /* ---------- Hero ---------- */
    .lp-hero{
        background:
            radial-gradient(900px 420px at 10% -20%, rgba(231,199,122,.24), transparent 60%),
            radial-gradient(700px 380px at 115% 8%, rgba(24,169,87,.12), transparent 55%),
            linear-gradient(160deg, #1E4FA6 0%, #0F2A4D 100%);
        color:#fff; position:relative; overflow:hidden; border-radius:28px;
    }
    /* Foto suasana kedai — set var --lp-hero-image pada elemen untuk aktifkan.
       Foto duduk di belakang lapisan warna gelap supaya teks putih kekal jelas. */
    .lp-hero::before{
        content:""; position:absolute; inset:0; pointer-events:none; z-index:0;
        background-image:
            linear-gradient(115deg, rgba(15,42,77,.94) 22%, rgba(15,42,77,.60) 78%, rgba(15,42,77,.78) 100%),
            var(--lp-hero-image, none);
        background-size:cover;
        background-position:center, right center;
        background-repeat:no-repeat;
    }
    .lp-hero::after{
        content:""; position:absolute; inset:0; pointer-events:none;
        background-image:
            linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
        background-size:46px 46px;
        -webkit-mask-image:radial-gradient(760px 420px at 82% 0%, #000, transparent 72%);
                mask-image:radial-gradient(760px 420px at 82% 0%, #000, transparent 72%);
    }
    .lp-hero > *{ position:relative; z-index:1; }
    .lp-hero .badge-pill-brass{
        background:rgba(231,199,122,.16); color:var(--brass-soft);
        font-family:'JetBrains Mono',monospace; letter-spacing:1px; font-size:.72rem;
    }
    .lp-trust{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; }
    .lp-stars i{ color:#F5C451; font-size:.95rem; }
    .lp-avatars{ display:flex; }
    .lp-avatars span{
        width:36px; height:36px; border-radius:50%; margin-left:-11px;
        border:2px solid rgba(255,255,255,.9);
        display:flex; align-items:center; justify-content:center;
        font-family:'Oswald',sans-serif; font-size:.82rem; color:#0F2A4D;
    }
    .lp-avatars span:first-child{ margin-left:0; }

    .lp-ticket-stack{ position:relative; min-height:380px; }
    .lp-float{ animation:lp-bob 5.5s ease-in-out infinite; }
    .lp-float.delay{ animation-delay:-2.6s; }
    @keyframes lp-bob{ 0%,100%{ transform:translateY(0) } 50%{ transform:translateY(-13px) } }
    .lp-nowserving{
        position:absolute; right:-6px; bottom:6px; width:220px;
        background:#fff; color:var(--ink); border:1px solid var(--line);
        border-radius:16px; padding:15px 17px; box-shadow:var(--shadow-lg);
    }
    .lp-nowserving .live-dot{
        width:9px; height:9px; border-radius:50%; background:var(--green-ok);
        display:inline-block; box-shadow:0 0 0 4px var(--green-light);
    }
    @media (prefers-reduced-motion:reduce){ .lp-float{ animation:none } }

    /* ---------- Style marquee ---------- */
    .lp-marquee{
        overflow:hidden; padding:16px 0; border-block:1px solid var(--line);
        -webkit-mask-image:linear-gradient(90deg, transparent, #000 7%, #000 93%, transparent);
                mask-image:linear-gradient(90deg, transparent, #000 7%, #000 93%, transparent);
    }
    .lp-marquee ul{
        display:flex; gap:12px; list-style:none; margin:0; padding:0; width:max-content;
        animation:lp-scroll 30s linear infinite;
    }
    .lp-marquee:hover ul{ animation-play-state:paused; }
    .lp-marquee li{
        font-family:'Oswald',sans-serif; text-transform:uppercase; letter-spacing:1px;
        font-size:.82rem; color:var(--pine-deep); background:var(--pine-light);
        border-radius:999px; padding:8px 18px; white-space:nowrap;
    }
    .lp-marquee li .bi{ color:var(--brass); }
    @keyframes lp-scroll{ to{ transform:translateX(-50%) } }
    @media (prefers-reduced-motion:reduce){ .lp-marquee ul{ animation:none } }

    /* ---------- Stat band ---------- */
    .lp-statband{
        background:linear-gradient(160deg, #EEF4FF 0%, #FBF3E0 100%);
        border:1px solid var(--line); border-radius:var(--lp-radius);
    }
    .lp-statnum{ font-family:'Oswald',sans-serif; font-size:2.5rem; line-height:1; color:var(--pine); }

    /* ---------- Steps ---------- */
    .lp-steps{ position:relative; }
    .lp-step-num{
        width:46px; height:46px; border-radius:13px; margin:0 auto 14px;
        background:linear-gradient(160deg, var(--pine), var(--pine-deep)); color:#fff;
        font-family:'Oswald',sans-serif; font-size:1.25rem;
        display:flex; align-items:center; justify-content:center;
        box-shadow:0 6px 16px rgba(33,88,184,.3);
    }
    @media (min-width:768px){
        .lp-steps::before{
            content:""; position:absolute; top:23px; left:14%; right:14%; height:2px;
            background:repeating-linear-gradient(90deg, var(--line) 0 7px, transparent 7px 15px);
        }
    }

    /* ---------- Testimonial carousel ---------- */
    .lp-testi .carousel-item{ padding:6px 4px 8px; }
    .lp-quote{
        max-width:760px; margin:0 auto; text-align:center; padding:36px 34px;
        background:#fff; border:1px solid var(--line); border-radius:var(--lp-radius);
        box-shadow:var(--shadow-md);
    }
    .lp-quote .bi-quote{ font-size:2.4rem; color:var(--brass); }
    .lp-quote .lp-quote-text{ font-size:1.16rem; line-height:1.6; }
    .lp-quote .lp-author{ font-family:'Oswald',sans-serif; text-transform:uppercase; letter-spacing:.5px; }
    .lp-quote .lp-shop{ color:var(--pine); }
    .lp-testi .carousel-control-prev,
    .lp-testi .carousel-control-next{
        width:44px; height:44px; top:50%; transform:translateY(-50%); opacity:1;
        background:#fff; border:1px solid var(--line); border-radius:50%; box-shadow:var(--shadow-sm);
    }
    .lp-testi .carousel-control-prev{ left:-8px; }
    .lp-testi .carousel-control-next{ right:-8px; }
    .lp-testi .carousel-control-prev-icon,
    .lp-testi .carousel-control-next-icon{ filter:invert(1) grayscale(1); width:1rem; height:1rem; }
    .lp-testi .carousel-indicators{ bottom:-46px; margin-bottom:0; }
    .lp-testi .carousel-indicators [data-bs-target]{
        width:9px; height:9px; border-radius:50%; background:var(--pine); border:0; opacity:.35;
    }
    .lp-testi .carousel-indicators .active{ opacity:1; }
    @media (max-width:575.98px){
        .lp-testi .carousel-control-prev,
        .lp-testi .carousel-control-next{ display:none; }
        .lp-quote{ padding:28px 20px; }
    }

    /* ---------- FAQ ---------- */
    .lp-faq .accordion-item{
        border:1px solid var(--line); border-radius:14px !important;
        margin-bottom:10px; overflow:hidden;
    }
    .lp-faq .accordion-button{
        font-family:'Oswald',sans-serif; text-transform:uppercase; letter-spacing:.3px;
        font-size:.96rem;
    }
    .lp-faq .accordion-button:not(.collapsed){
        background:var(--pine-light); color:var(--pine-deep); box-shadow:none;
    }
    .lp-faq .accordion-button:focus{ box-shadow:none; }

    /* ---------- Scroll reveal ---------- */
    [data-reveal]{ opacity:0; transform:translateY(26px); transition:opacity .6s ease, transform .6s ease; }
    [data-reveal].in{ opacity:1; transform:none; }
    @media (prefers-reduced-motion:reduce){
        [data-reveal]{ opacity:1 !important; transform:none !important; transition:none; }
    }
</style>
@endpush

@section('navbar')
<nav class="navbar navbar-brand-bar navbar-dark py-3 sticky-top">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-2">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
            <span class="pole-dot"></span> Blade &amp; Fade
        </a>
        <div class="lp-navlinks">
            {{-- <a href="#macam-mana" class="btn btn-outline-light btn-sm d-none d-md-inline-block">Cara Guna</a>
            <a href="#harga" class="btn btn-outline-light btn-sm">Pakej</a>
            <a href="#testimoni" class="btn btn-outline-light btn-sm d-none d-md-inline-block">Testimoni</a>
            <a href="#soalan" class="btn btn-outline-light btn-sm d-none d-lg-inline-block">Soalan</a> --}}
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Log Masuk</a>
            <a href="{{ route('register.owner') }}" class="btn btn-brand btn-sm">Daftar Barbershop</a>
        </div>
    </div>
</nav>
@endsection

@section('content')
<div class="lp">

{{-- ============ HERO ============ --}}
<div class="lp-hero p-4 p-lg-5 mt-4 mt-lg-1 mb-5"
     style="--lp-hero-image:url('{{ asset('images/hero-barbershop.jpg') }}');">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge rounded-pill px-3 py-2 mb-3 badge-pill-brass">
                ⚡ GILIRAN DIGITAL · TANPA MUAT TURUN APLIKASI
            </span>
            <h1 class="font-display display-4 mb-3" style="line-height:1.08;">
                Kedai Anda.<br><span class="brass-text">Giliran Yang Tersusun.</span>
            </h1>
            <p class="mb-4 opacity-75 fs-5" style="max-width:520px;">
                Pelanggan imbas QR, ambil nombor, dan tunggu di mana sahaja. Tukang gunting urus giliran
                dengan satu ketikan — tiada lagi kertas, tiada lagi sesak di kaunter.
            </p>
            <div class="d-flex gap-3 flex-wrap mb-4">
                <a href="{{ route('register.owner') }}" class="btn btn-brand btn-lg px-4">Mulakan Percuma</a>
                <a href="#macam-mana" class="btn btn-outline-light btn-lg px-4">Lihat Cara Ia Berfungsi</a>
            </div>
            <div class="lp-trust">
                <div class="lp-avatars" aria-hidden="true">
                    <span style="background:#E7C77A;">DN</span>
                    <span style="background:#9CC3F5;">AM</span>
                    <span style="background:#7FD8A6;">HF</span>
                    <span style="background:#F2B4B6;">SY</span>
                </div>
                <div>
                    <div class="lp-stars">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <div class="small opacity-75">Dipercayai barbershop di seluruh Malaysia</div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 d-none d-lg-block">
            <div class="lp-ticket-stack">
                <div class="ticket-card lp-float" style="transform:rotate(-5deg); max-width:300px;">
                    <p class="eyebrow mb-0">Tiket Anda</p>
                    <div class="num">#014</div>
                    <p class="font-display mb-1">Haircut + Beard</p>
                    <p class="text-muted small mb-3">Anggaran ~18 minit</p>
                    <div class="border rounded p-2 bg-white small">Status: <strong>Menunggu</strong></div>
                </div>
                <div class="lp-nowserving lp-float delay">
                    <p class="eyebrow-label mb-1" style="color:var(--red);">Sedang Dilayan</p>
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="font-display" style="font-size:1.7rem; color:var(--pine);">#012</span>
                        <span class="live-dot"></span>
                    </div>
                    <p class="small text-muted mb-0">Fade + Beard · Kerusi 2</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ MARQUEE GAYA ============ --}}
<div class="lp-marquee mb-5" aria-hidden="true">
    <ul>
        @php $styles = ['Skin Fade','Undercut','Crew Cut','Beard Sculpt','Buzz Cut','Line Up','Pompadour','Kids Cut','Mullet','Taper','Perm','Hot Towel Shave']; @endphp
        @foreach (array_merge($styles, $styles) as $style)
            <li><i class="bi bi-scissors"></i> {{ $style }}</li>
        @endforeach
    </ul>
</div>

{{-- ============ JALUR STATISTIK ============ --}}
<div class="lp-statband p-4 p-lg-5 mb-5" data-reveal>
    <div class="row text-center g-4">
        <div class="col-6 col-lg-3">
            <div class="lp-statnum">200+</div>
            <div class="text-muted small mt-1">Barbershop guna sistem ini</div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="lp-statnum">50K+</div>
            <div class="text-muted small mt-1">Tiket giliran dijana</div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="lp-statnum">~15<span class="fs-6"> min</span></div>
            <div class="text-muted small mt-1">Masa menunggu dijimatkan</div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="lp-statnum">4.8<span class="fs-6">/5</span></div>
            <div class="text-muted small mt-1">Purata rating pemilik kedai</div>
        </div>
    </div>
</div>

{{-- ============ CARA IA BERFUNGSI ============ --}}
<div id="macam-mana" class="lp-block mb-5 pt-3">
    <div class="text-center mb-5" data-reveal>
        <p class="eyebrow-label mb-2">Cara Ia Berfungsi</p>
        <h2 class="font-display">Dari Imbasan ke Gunting — Tiga Langkah</h2>
    </div>
    <div class="row g-4 text-center lp-steps">
        <div class="col-md-4" data-reveal>
            <div class="card card-brand h-100">
                <div class="card-body p-4">
                    <div class="lp-step-num">1</div>
                    <div class="feature-icon mx-auto"><i class="bi bi-qr-code-scan"></i></div>
                    <h5 class="font-display">Imbas &amp; Pilih Servis</h5>
                    <p class="text-muted small mb-0">Pelanggan imbas QR di kaunter, masukkan bilangan orang &amp; pilih servis untuk setiap seorang.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-reveal>
            <div class="card card-brand h-100">
                <div class="card-body p-4">
                    <div class="lp-step-num">2</div>
                    <div class="feature-icon mx-auto"><i class="bi bi-ticket-perforated"></i></div>
                    <h5 class="font-display">Dapat Nombor Giliran</h5>
                    <p class="text-muted small mb-0">Tiket digital terus dijana dengan anggaran masa menunggu yang dikira secara langsung.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4" data-reveal>
            <div class="card card-brand h-100">
                <div class="card-body p-4">
                    <div class="lp-step-num">3</div>
                    <div class="feature-icon mx-auto"><i class="bi bi-scissors"></i></div>
                    <h5 class="font-display">Tukang Gunting Urus Sendiri</h5>
                    <p class="text-muted small mb-0">Panggil pelanggan seterusnya bila betul-betul ready — giliran kekal adil dan telus.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ TESTIMONI (CAROUSEL) ============ --}}
<div id="testimoni" class="lp-block mb-5 pt-3">
    <div class="text-center mb-5" data-reveal>
        <p class="eyebrow-label mb-2">Kata Mereka</p>
        <h2 class="font-display">Tukang Gunting Yang Dah Guna</h2>
    </div>

    <div class="lp-testi" data-reveal>
        <div id="testiCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
            <div class="carousel-inner">
                @php
                    $testimonials = [
                        ['q' => 'Blade & Fade betul-betul buang masalah pelanggan bertindih kat pintu. Sekarang diorang scan, pergi minum kejap, balik bila dekat giliran.', 'name' => 'En. Syafiq', 'shop' => 'Kapster Co. · Shah Alam'],
                        ['q' => 'Report komisen tiap barber keluar automatik. Hujung bulan tak payah lagi duduk kira guna kalkulator sampai pukul 2 pagi.', 'name' => 'Amir Hakim', 'shop' => "Gentlemen's Code · Kuantan"],
                        ['q' => 'Tiga cawangan, satu skrin. Aku boleh tengok mana satu sibuk dari rumah, terus telefon barber tambah shift.', 'name' => 'Hafiz Rahman', 'shop' => 'The Cut Bros · Ipoh'],
                        ['q' => 'Pelanggan baru selalu tanya berapa lama nak tunggu. Sekarang app dah tunjuk anggaran sendiri — kurang dah orang membebel kat kaunter.', 'name' => 'Din', 'shop' => 'Kedai Gunting Abang Din · Kajang'],
                    ];
                @endphp
                @foreach ($testimonials as $i => $t)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <div class="lp-quote">
                            <i class="bi bi-quote"></i>
                            <div class="lp-stars mb-3">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            </div>
                            <p class="lp-quote-text mb-4">{{ $t['q'] }}</p>
                            <p class="lp-author mb-0">{{ $t['name'] }}</p>
                            <p class="small lp-shop mb-0">{{ $t['shop'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#testiCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Sebelum</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testiCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Seterusnya</span>
            </button>

            <div class="carousel-indicators">
                @foreach ($testimonials as $i => $t)
                    <button type="button" data-bs-target="#testiCarousel" data-bs-slide-to="{{ $i }}"
                            class="{{ $i === 0 ? 'active' : '' }}" aria-label="Testimoni {{ $i + 1 }}"></button>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ============ PENGURUSAN PENUH ============ --}}
<div class="row g-4 mb-5 align-items-center pt-4">
    <div class="col-lg-6" data-reveal>
        <p class="eyebrow-label mb-2">Satu Dashboard, Semua Kawalan</p>
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
    <div class="col-lg-6" data-reveal>
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
<div id="harga" class="lp-block mb-5 pt-3">
    <div class="text-center mb-5" data-reveal>
        <p class="eyebrow-label mb-2">Pilih Pakej Anda</p>
        <h2 class="font-display">Harga Yang Sesuai Untuk Setiap Saiz Barbershop</h2>
        <p class="text-muted">Setiap pakej datang dengan peruntukan point bulanan — 1 point ditolak setiap tugasan siap.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach ($plans as $index => $plan)
            @php $popular = $index === 1; @endphp
            <div class="col-md-4" data-reveal>
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

{{-- ============ SOALAN LAZIM ============ --}}
<div id="soalan" class="lp-block mb-5 pt-3">
    <div class="text-center mb-5" data-reveal>
        <p class="eyebrow-label mb-2">Soalan Lazim</p>
        <h2 class="font-display">Perkara Yang Selalu Ditanya</h2>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion lp-faq" id="faqAccordion" data-reveal>
                @php
                    $faqs = [
                        ['q' => 'Pelanggan perlu muat turun aplikasi?', 'a' => 'Tak perlu. Pelanggan cuma imbas QR di kaunter dan terus dapat tiket giliran dalam browser telefon mereka.'],
                        ['q' => 'Boleh cuba dulu sebelum bayar?', 'a' => 'Boleh. Daftar barbershop anda percuma dan uji sistem giliran tanpa kad kredit. Naik taraf pakej bila anda dah sedia.'],
                        ['q' => 'Macam mana kalau ada lebih satu cawangan?', 'a' => 'Setiap cawangan ada giliran dan QR tersendiri, tetapi semuanya diurus dari satu akaun pemilik yang sama.'],
                        ['q' => 'Sistem kira komisen tukang gunting sekali?', 'a' => 'Ya. Tetapkan kadar komisen atau sewa kerusi bagi setiap barber, dan laporan pendapatan dikira automatik setiap tugasan siap.'],
                    ];
                @endphp
                @foreach ($faqs as $i => $faq)
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHead{{ $i }}">
                            <button class="accordion-button {{ $i === 0 ? '' : 'collapsed' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faqBody{{ $i }}"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="faqBody{{ $i }}">
                                {{ $faq['q'] }}
                            </button>
                        </h3>
                        <div id="faqBody{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                             aria-labelledby="faqHead{{ $i }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small">{{ $faq['a'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ============ HUBUNGI KAMI ============ --}}
<div id="hubungi" class="lp-block mb-5 pt-3">
    <div class="row g-4 align-items-center">
        <div class="col-lg-5" data-reveal>
            <p class="eyebrow-label mb-2">Ada Soalan?</p>
            <h2 class="font-display mb-3">Hubungi Kami</h2>
            <p class="text-muted mb-0">
                Belum pasti pakej mana sesuai untuk kedai anda? Isikan borang ni, pasukan kami akan hubungi
                anda tak lama lagi untuk bantu pilih pakej yang paling sesuai.
            </p>
        </div>
        <div class="col-lg-7" data-reveal>
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
<div id="daftar" class="lp-hero rounded-4 mb-5">
    <div class="card-body p-5 text-center">
        <h3 class="font-display mb-2">Sedia Untuk Mulakan?</h3>
        <p class="opacity-75 mb-4">Daftar barbershop anda dalam masa kurang 2 minit — tiada kad kredit diperlukan untuk mula.</p>
        <a href="{{ route('register.owner') }}" class="btn btn-brand btn-lg px-4">Daftar Barbershop Sekarang</a>
    </div>
</div>

</div>{{-- /.lp --}}
@endsection

@push('scripts')
<script>
    (function () {
        var nav = document.querySelector('.navbar-brand-bar');
        if (nav) {
            var onScroll = function () { nav.classList.toggle('is-scrolled', window.scrollY > 20); };
            onScroll();
            window.addEventListener('scroll', onScroll, { passive: true });
        }

        var targets = document.querySelectorAll('[data-reveal]');
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('in');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            targets.forEach(function (el) { io.observe(el); });
        } else {
            targets.forEach(function (el) { el.classList.add('in'); });
        }
    })();
</script>
@endpush

@php $whatsappLink = \App\Models\Setting::whatsappLink(); @endphp
@if ($whatsappLink)
    @push('scripts')
    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"
       style="position:fixed; bottom:24px; right:24px; z-index:1050; background:#25D366; color:#fff; width:56px; height:56px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 14px rgba(0,0,0,.25); font-size:1.6rem; text-decoration:none;"
       aria-label="Hubungi kami melalui WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
    @endpush
@endif
