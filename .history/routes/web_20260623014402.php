<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{CabangController, MenuController, UserController, BeliBahanController, ToppingController, ProfileController, LoginController, LaporanController, DashboardController};
use App\Http\Controllers\Kasir\PesanKasirController;

// 1. RUTE PUBLIK (Tanpa Login)
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// 2. RUTE DENGAN LOGIN (Autentikasi)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- RUTE KASIR ---
    Route::prefix('kasir')->name('kasir.')->group(function () {
        Route::controller(PesanKasirController::class)->group(function () {
            Route::get('/pesan', 'index')->name('pesan.index');
            Route::post('/pesan/add', 'addToCart')->name('pesan.add');
            Route::post('/pesan/checkout', 'checkout')->name('pesan.checkout');
            Route::get('/pesan/remove/{index}', 'removeCart')->name('pesan.remove');
            Route::get('/pesan/update-qty/{index}/{action}', 'updateQty')->name('pesan.update-qty');
            Route::get('/pesan/clear', 'clearCart')->name('pesan.clear');
            Route::get('/pesan/print/{id}', 'printStruk')->name('pesan.print');
            Route::get('/riwayat', 'index')->name('riwayat.index');
        });
        // Profile Kasir
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    });

    // --- RUTE ADMIN (Gunakan middleware 'role:admin' di sini) ---
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('cabang', CabangController::class);
        Route::resource('menu', MenuController::class);
        Route::resource('user', UserController::class)->parameters(['user' => 'id']);
        Route::resource('toping', ToppingController::class);
        Route::resource('pembelian', BeliBahanController::class);

        // Laporan
        Route::get('/laporan/harian', [LaporanController::class, 'harian'])->name('laporan.harian');
        Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan'])->name('laporan.bulanan');
        Route::get('/laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan.tahunan');

        // Profile Admin
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'index')->name('profile.index');
            Route::put('/profile/update', 'update')->name('profile.update');
            Route::put('/profile/password', 'updatePassword')->name('profile.password');
        });
    });
});