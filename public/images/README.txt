Foto latar hero halaman utama (landing page)
=============================================

Letak SATU fail foto di folder ini dengan nama:

    hero-barbershop.jpg

Gambar yang sesuai:
  - Suasana pelanggan sedang menunggu di kedai gunting, atau
    tukang gunting sedang menggunting rambut pelanggan.
  - Landskap (mendatar), sekurang-kurangnya 1600 x 1000 px.
  - Saiz fail elok < 400 KB (mampatkan dulu di tinypng.com atau squoosh.app).
  - Format .jpg (kalau .png atau .webp, tukar nama fail dalam
    resources/views/landing.blade.php pada baris:
        style="--lp-hero-image:url('{{ asset('images/hero-barbershop.jpg') }}');"

Sumber foto percuma (bebas guna komersial):
  - unsplash.com  -> cari "barbershop"
  - pexels.com    -> cari "barber shop haircut"

Fail yang sama (hero-barbershop.jpg) digunakan untuk DUA tempat:
  1. Latar belakang hero (di belakang lapisan biru gelap).
  2. Latar belakang seluruh halaman / kawasan putih (di belakang lapisan
     warna terang ~94% supaya kad & teks kekal jelas).

Kalau mahu foto berbeza untuk latar halaman, tukar URL pada blok "body{...}"
di dalam resources/views/landing.blade.php.

Selepas letak fail, refresh halaman. Tiada lain perlu diubah.
