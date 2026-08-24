<?php

use App\Http\Controllers\BarberController;
use App\Http\Controllers\QueueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC — pelanggan scan QR (tiada login)
|--------------------------------------------------------------------------
*/
Route::get('/q/{qrToken}', [QueueController::class, 'show'])->name('queue.show');
Route::post('/q/{qrToken}', [QueueController::class, 'store'])->name('queue.store');
Route::get('/ticket-group/{group}', [QueueController::class, 'showGroup'])->name('queue.group.show');
Route::get('/branch/{qrToken}/display', [QueueController::class, 'display'])->name('queue.display');
Route::get('/pricing', function () {
    return redirect(route('landing') . '#harga');
})->name('pricing');

// Callback ToyyibPay (server-to-server, BUKAN dari browser pelanggan) — kena
// public & dikecualikan dari CSRF. Lihat nota di bootstrap/app.php.
Route::post('/toyyibpay/callback', [\App\Http\Controllers\Owner\SubscriptionController::class, 'callback'])->name('toyyibpay.callback');

/*
|--------------------------------------------------------------------------
| AUTH — semua role kena login dulu
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Route ini diperlukan oleh Breeze (selepas login/verify email akan redirect sini)
    // — auto-agih ke dashboard yang betul ikut role (admin/barber/lain-lain).
    Route::get('/dashboard', \App\Http\Controllers\DashboardRedirectController::class)
        ->middleware('verified')->name('dashboard');

    // ---------------- BARBER ----------------
    Route::middleware(['role:barber'])->prefix('barber')->name('barber.')->group(function () {
        Route::get('/dashboard', [BarberController::class, 'dashboard'])->name('dashboard');
        Route::post('/shift/start', [BarberController::class, 'startShift'])->name('shift.start');
        Route::post('/shift/end', [BarberController::class, 'endShift'])->name('shift.end');
        Route::post('/call-next', [BarberController::class, 'callNext'])->name('call.next');
        Route::get('/earnings', [BarberController::class, 'earnings'])->name('earnings');
        Route::post('/ticket/{ticket}/start', [BarberController::class, 'start'])->name('ticket.start');
        Route::post('/ticket/{ticket}/next', [BarberController::class, 'next'])->name('ticket.next');
        Route::post('/ticket/{ticket}/skip', [BarberController::class, 'skip'])->name('ticket.skip');
    });

    // ---------------- OWNER ----------------
    Route::middleware(['role:owner'])->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('branches', \App\Http\Controllers\Owner\OwnerBranchController::class)->except(['show']);
        Route::get('/branches/{branch}/qr', [\App\Http\Controllers\Owner\OwnerBranchController::class, 'qrPrint'])->name('branches.qr');
        Route::resource('branches.barbers', \App\Http\Controllers\Owner\OwnerBarberController::class)
            ->shallow()->except(['show']);
        Route::resource('branches.services', \App\Http\Controllers\Owner\OwnerServiceController::class)
            ->shallow()->except(['show']);
        Route::get('/report', [\App\Http\Controllers\Owner\OwnerReportController::class, 'index'])->name('report');
        Route::get('/subscription', [\App\Http\Controllers\Owner\SubscriptionController::class, 'index'])->name('subscription.index');
        Route::get('/subscription/{plan}/checkout', [\App\Http\Controllers\Owner\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
        Route::post('/subscription/{plan}/confirm', [\App\Http\Controllers\Owner\SubscriptionController::class, 'confirm'])->name('subscription.confirm');
        Route::get('/subscription-return', [\App\Http\Controllers\Owner\SubscriptionController::class, 'returnPage'])->name('subscription.return');
        Route::get('/points', [\App\Http\Controllers\Owner\PointController::class, 'index'])->name('points.index');
        Route::get('/tukang-gunting', [\App\Http\Controllers\Owner\BarberOverviewController::class, 'index'])->name('barbers.index');
        Route::get('/tickets/served', [\App\Http\Controllers\Owner\TicketController::class, 'served'])->name('tickets.served');
        Route::get('/tickets/waiting', [\App\Http\Controllers\Owner\TicketController::class, 'waiting'])->name('tickets.waiting');
        Route::get('/tickets/history', [\App\Http\Controllers\Owner\TicketController::class, 'history'])->name('tickets.history');
    });

    // ---------------- AGENT ----------------
    Route::middleware(['role:agent'])->prefix('agent')->name('agent.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Agent\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/daftar-barbershop', [\App\Http\Controllers\Agent\RegisterOwnerController::class, 'create'])->name('register-owner.create');
        Route::post('/daftar-barbershop', [\App\Http\Controllers\Agent\RegisterOwnerController::class, 'store'])->name('register-owner.store');
        Route::get('/owners', [\App\Http\Controllers\Agent\OwnerSearchController::class, 'index'])->name('owners.index');
    });

    // ---------------- SUPER ADMIN ----------------
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('agents', \App\Http\Controllers\Admin\AgentController::class)->except(['show']);
        Route::get('/commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
        Route::post('/commissions/{commission}/mark-paid', [\App\Http\Controllers\Admin\CommissionController::class, 'markPaid'])->name('commissions.markPaid');
        Route::resource('plans', \App\Http\Controllers\Admin\PlanController::class)->except(['show']);
        Route::resource('topup-packages', \App\Http\Controllers\Admin\TopupPackageController::class)->except(['show']);
        Route::get('/topups', [\App\Http\Controllers\Admin\TopupController::class, 'index'])->name('topups.index');
        Route::get('/logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('/owners', [\App\Http\Controllers\Admin\OwnerController::class, 'index'])->name('owners.index');
        Route::get('/owners/{owner}', [\App\Http\Controllers\Admin\OwnerController::class, 'show'])->name('owners.show');
        Route::post('/owners/{owner}/activate', [\App\Http\Controllers\Admin\OwnerController::class, 'activate'])->name('owners.activate');
        Route::post('/owners/{owner}/renewal-mode', [\App\Http\Controllers\Admin\OwnerController::class, 'updateRenewalMode'])->name('owners.renewalMode.update');
        Route::post('/owners/{owner}/grant-trial', [\App\Http\Controllers\Admin\OwnerController::class, 'grantTrial'])->name('owners.grantTrial');
        Route::get('/branches', [\App\Http\Controllers\Admin\BranchController::class, 'index'])->name('branches.index');
        Route::get('/subscriptions', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions/{subscription}/recheck', [\App\Http\Controllers\Admin\SubscriptionController::class, 'recheck'])->name('subscriptions.recheck');
        Route::get('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
        // Route::resource('plans', AdminPlanController::class);
        // Route::resource('owners', AdminOwnerController::class);
    });

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| LANDING PAGE + PENDAFTARAN OWNER (dengan/tanpa kod agent)
|--------------------------------------------------------------------------
*/
Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');

// Alias 'home' — sesetengah view/paket bawaan Laravel & Breeze memanggil route('home')
// (cth. lepas logout, atau link "back to home" pada error page). Route ini elak error
// "Route [home] not defined" tanpa perlu kita cari & tukar setiap rujukan tu satu-satu.
Route::get('/home', function () {
    return redirect()->route('landing');
})->name('home');

Route::get('/daftar', [\App\Http\Controllers\RegisterOwnerController::class, 'create'])->name('register.owner');
Route::post('/daftar', [\App\Http\Controllers\RegisterOwnerController::class, 'store'])->name('register.owner.store');

require __DIR__.'/auth.php';

// Override POST /login Breeze — tambah semakan akaun belum aktif. PENTING:
// ni kena letak SELEPAS require auth.php di atas, sebab Laravel guna route
// yang PALING AKHIR didaftar bila method+URL sama persis (bukan yang awal).
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])
    ->middleware('guest');
Route::post('/resend-verification', [\App\Http\Controllers\Auth\LoginController::class, 'resendVerification'])
    ->middleware('guest')->name('verification.resend.guest');

// Override GET email/verify/{id}/{hash} Breeze — route asal perlukan pengguna
// SUDAH login (middleware 'auth') untuk berfungsi. Sejak kita tak auto-login
// lepas daftar, ni cipta deadlock (kena login utk verify, tapi tak boleh login
// sebab belum verify). Route ni cari user terus dari {id}, tak perlukan sesi.
// PENTING: URI kena SAMA PERSIS dengan Breeze (email/verify, bukan verify-email)
// supaya betul-betul menimpa, bukan jadi route berasingan.
Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\LoginController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\LoginController::class, 'sendResetLink'])
    ->middleware('guest')->name('password.email');
