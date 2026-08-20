# Barbershop Queue System — Starter (Laravel + MySQL + Bootstrap 5)

## 1. Setup projek

```bash
composer create-project laravel/laravel barbershop-queue
cd barbershop-queue

composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

> Nota: package `spatie/laravel-permission` **TIDAK diperlukan**. Sistem role
> kita guna table `roles` + `users.role_id` sendiri, dengan middleware
> custom `app/Http/Middleware/EnsureUserHasRole.php`.

## 2. Salin fail dari starter ini

Salin semua ke lokasi sepadan dalam projek awak (timpa yang sedia ada):
- `database/migrations/*`
- `app/Models/*`
- `app/Http/Controllers/*` (termasuk subfolder `Admin/`)
- `app/Http/Middleware/EnsureUserHasRole.php`
- `database/seeders/AdminSeeder.php`
- `resources/views/*` (termasuk subfolder `auth/`, `queue/`, `barber/`, `admin/`, `layouts/`)
- `public/css/app.css`
- `routes/web.php`

## 3. Daftarkan middleware `role`

Buka `bootstrap/app.php`, dalam method `withMiddleware`, tambah:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
```

## 4. Setup .env, migrate & seed

```bash
# Set DB_DATABASE, DB_USERNAME, DB_PASSWORD dalam .env
php artisan migrate:fresh
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=SubscriptionPlanSeeder
php artisan db:seed --class=TopupPackageSeeder
```

⚠️ Kalau projek sedia ada (dah pernah migrate sebelum ni), guna `migrate:fresh` sekali
lagi (buang semua data) supaya struktur `point_batches` & `subscription_plans.points_included`
yang baru terpakai betul. Kalau tak nak buang data sedia ada, run je
`php artisan migrate` (tanpa `:fresh`) untuk tambah 2 migration baru sahaja.

### Akaun Admin Default

```
Emel     : admin@barbershop.test
Password : Admin@12345
```

⚠️ **Tukar password ni lepas login kali pertama** (guna halaman Profile Breeze
di `/profile`) — ni cuma credential sementara untuk development.

Log masuk di `http://localhost:8000/login` — sistem akan auto-redirect ke
`/admin/dashboard` sebab role akaun ni `super_admin`.

## 5. Setup emel pengesahan akaun

Owner akan terima emel pengesahan (verify email) sebaik sahaja selesai daftar
di `/daftar`. Untuk development, set dalam `.env`:

```
MAIL_MAILER=log
```

Emel yang "dihantar" akan tersimpan dalam `storage/logs/laravel.log` (cari
pautan `http://localhost:8000/verify-email/...` dalam fail tu dan buka terus
di browser untuk simulasi pelanggan klik pautan emel). Untuk lihat emel dalam
UI yang cantik semasa development, boleh guna [Mailtrap](https://mailtrap.io)
(percuma) — set `MAIL_MAILER=smtp` dengan credential Mailtrap dalam `.env`.

## 6. Daftar cawangan & tukang gunting

Log masuk sebagai **owner** (guna akaun yang didaftar melalui `/daftar`),
sistem akan bawa ke `/owner/dashboard`. Dari situ:

1. Klik **"+ Tambah Cawangan"** — isi nama, alamat, no. telefon.
2. Klik **"Urus Tukang Gunting"** pada cawangan tu → **"+ Daftar Tukang Gunting"**.
3. Isi nama, emel, telefon, kata laluan — akaun login untuk tukang gunting
   tercipta **automatik** sekali dengan pendaftaran (tak perlu emel pengesahan,
   sebab owner yang daftar terus).
4. Tukang gunting log masuk guna emel/password tu di `/login`, auto-redirect
   ke `/barber/dashboard`.

4. Klik **"Servis"** pada cawangan tu → **"+ Tambah Servis"** untuk tambah servis
   (cth: Haircut RM25, 20 minit) — pelanggan tak boleh ambil nombor giliran
   sehingga sekurang-kurangnya satu servis aktif wujud.

## 7. Bahasa Melayu untuk mesej sistem

Buka `.env`, tukar:
```
APP_LOCALE=en
```
kepada:
```
APP_LOCALE=ms
```
Ini buat semua mesej validation bawaan Laravel (cth: "field wajib diisi") papar
dalam Bahasa Melayu — fail terjemahan `lang/ms/*.php` dah disediakan.

## 8. Jenama emel & tajuk browser

Buka `.env`, tukar:
```
APP_NAME=Laravel
```
kepada:
```
APP_NAME="Blade & Fade"
```
Ini betulkan nama jenama pada `<title>` tab browser dan bahagian footer/header
emel bawaan Laravel. Header & tema warna emel juga dah dikustomkan penuh
(`resources/views/vendor/mail/`) — warna & ikon pole 💈 "Blade & Fade" terus
terpakai tanpa perlu setting tambahan.

Favicon (ikon tab browser) juga dah dijana — `public/favicon.ico`,
`favicon-32x32.png`, `apple-touch-icon.png` — reka bentuk pole gunting rambut
(belang merah-putih-biru dalam bulatan navy). Tak perlu setting tambahan,
cuma clear cache browser (`Ctrl+Shift+R`) kalau ikon lama masih tersimpan.

## 9. Waktu Malaysia (penting!)

Buka `.env`, cari/tambah baris:
```
APP_TIMEZONE=Asia/Kuala_Lumpur
```
Tanpa ni, Laravel default guna UTC — semua masa (log masuk barber, tiket
selesai, shift, dsb) akan papar **8 jam lewat** dari waktu Malaysia sebenar.
Satu baris ni betulkan SEMUA masa dalam sistem serentak (tak perlu ubah kod).

⚠️ Rekod yang **dah sedia ada** dalam database (dicipta sebelum setting ni
ditukar) akan kekal ikut waktu lama yang tersimpan — cuma rekod **baru**
lepas ni akan betul ikut waktu Malaysia.

## 10. Sistem Log Aktiviti

Semua emel yang dihantar, pemilihan subscription, topup point, dan pembayaran
komisen ejen kini direkod dalam table `activity_logs`. Admin boleh lihat di
`/admin/logs` (menu "Log" dalam navbar admin) — boleh tapis ikut jenis.

## 11. Integrasi ToyyibPay (Payment Gateway)

### 11.1 Daftar akaun ToyyibPay
1. Untuk testing: daftar di https://dev.toyyibpay.com (Sandbox — bayaran palsu, guna Bank Simulator)
2. Untuk production sebenar: daftar di https://toyyibpay.com
3. Dalam dashboard, cipta satu **Category** (cth: "Blade & Fade Subscription") — copy `categoryCode` yang dijana
4. Cari `userSecretKey` di bahagian bawah dashboard utama — copy

### 11.2 Tambah config/services.php
Buka `config/services.php`, tambah blok ni dalam array yang dipulangkan (rujuk
`config/services-toyyibpay-snippet.txt` dalam zip ni untuk kandungan tepat):

```php
'toyyibpay' => [
    'base_url' => env('TOYYIBPAY_BASE_URL', 'https://dev.toyyibpay.com'),
    'secret_key' => env('TOYYIBPAY_SECRET_KEY'),
    'category_code' => env('TOYYIBPAY_CATEGORY_CODE'),
],
```

### 11.3 Tambah dalam .env
```
TOYYIBPAY_BASE_URL=https://dev.toyyibpay.com
TOYYIBPAY_SECRET_KEY=secretkey-anda-dari-dashboard
TOYYIBPAY_CATEGORY_CODE=categorycode-anda-dari-dashboard
```
⚠️ Bila dah sedia untuk production sebenar, tukar `TOYYIBPAY_BASE_URL` kepada
`https://toyyibpay.com` dan guna credential akaun production (bukan sandbox).

### 11.4 KECUALIKAN callback dari CSRF (WAJIB)
ToyyibPay hantar callback terus dari server dia (bukan browser pelanggan),
jadi laluan tu MESTI dikecualikan dari perlindungan CSRF Laravel, atau
pembayaran akan gagal disahkan.

Buka `bootstrap/app.php`, dalam `->withMiddleware(function (Middleware $middleware) {`
tambah:

```php
$middleware->validateCsrfTokens(except: [
    'toyyibpay/callback',
]);
```

Bentuk lengkap patut nampak macam ni:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
    $middleware->validateCsrfTokens(except: [
        'toyyibpay/callback',
    ]);
})
```

### 11.5 Testing tempatan (localhost)
ToyyibPay perlukan URL callback yang **boleh diakses dari internet** (bukan
`localhost`). Untuk testing tempatan, guna terowong macam **ngrok** atau
fungsi "Share" Herd untuk dapatkan URL awam sementara, dan set `APP_URL`
dalam `.env` kepada URL tu sebelum test bayaran.

### 11.6 Cara ia berfungsi
- **Owner mod "online"** (default): pilih pakej → terus redirect ke ToyyibPay
  → bayar → ToyyibPay hantar callback ke sistem kita → subscription +
  point diaktifkan **automatik** lepas bayaran sah disahkan (bukan berdasarkan
  redirect browser sahaja, demi keselamatan)
- **Owner mod "offline"**: pilih pakej → terus sahkan tanpa gateway (macam
  sebelum ni) — sesuai untuk owner yang bayar cash/bank transfer terus
  kepada admin/kedai
- Admin boleh tukar mod ni bila-bila masa di page detail owner
  (`/admin/owners/{id}`) — butang "Tukar ke Online/Offline"

## 12. Yang belum dibina (fasa seterusnya)

- [ ] CRUD Admin: urus pakej (`subscription_plans`), agent, owner
- [ ] Flow subscription + payment gateway sebenar (buat masa ni owner terus `active` lepas daftar)
- [ ] Modul Agent: dashboard referral + commission + dashboard sendiri
- [ ] Generate QR code sebenar (`simplesoftwareio/simple-qrcode`) dari `branches.qr_token`
- [ ] Report harian owner (lebih detail — carta, eksport)

## Struktur folder starter ini

```
database/migrations/         14 migration (roles → queue_counters)
database/seeders/             AdminSeeder.php (role + akaun admin default)
app/Models/                   14 model + relationship
app/Http/Middleware/          EnsureUserHasRole.php (role custom, bukan spatie)
app/Http/Controllers/         QueueController, BarberController, LandingController,
                               RegisterOwnerController, DashboardRedirectController,
                               Admin/DashboardController
routes/web.php                skeleton route ikut role
resources/views/
  layouts/site.blade.php      layout brand kita (landing/queue/barber/admin)
  landing.blade.php           landing page + seksyen Harga (#harga)
  auth/                       login, forgot-password, reset-password, register-owner
  queue/                      form, ticket, display
  barber/dashboard.blade.php
  admin/dashboard.blade.php
```

## Nota logik penting

- **Role & akses**: `role:super_admin`, `role:owner`, `role:agent`, `role:barber`
  guna middleware custom kita, BUKAN spatie/laravel-permission.
- **Nombor giliran**: `QueueCounter::nextNumber($branchId)` — atomic, reset
  ikut cawangan + tarikh.
- **Auto-assign barber**: `Barber::nextAvailable($branchId)` — barber paling
  lama rehat diutamakan.
- **Redirect lepas login**: `/dashboard` auto-agih ikut role
  (`DashboardRedirectController`) — admin → `/admin/dashboard`,
  barber → `/barber/dashboard`.
