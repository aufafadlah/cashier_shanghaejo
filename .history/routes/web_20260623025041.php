<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TargetController;
use App\Http\Controllers\Admin\BeliBahanController;
use App\Http\Controllers\Admin\ToppingController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Kasir\PesanKasirController;
use App\Http\Controllers\Admin\LaporanTargetController;
use App\Http\Controllers\Admin\DashboardController;



// Grouping Route yang perlu Login
// --- PINDAHKAN KE SINI (DI LUAR AUTH UNTUK TESTING) ---
// --- ROUTE KASIR (Pindahkan semua ke sini agar satu grup) ---
Route::get('/', function () {
    return redirect()->route('login');
});
Route::controller(PesanKasirController::class)->group(function () {
    Route::get('/kasir/pesan', 'index')->name('kasir.pesan.index');
    Route::post('/kasir/pesan/add', 'addToCart')->name('kasir.pesan.add');
    Route::post('/kasir/pesan/checkout', 'checkout')->name('kasir.pesan.checkout');
    Route::get('/kasir/pesan/remove/{index}', 'removeCart')->name('kasir.pesan.remove');
    Route::get('/kasir/pesan/update-qty/{index}/{action}', 'updateQty')->name('kasir.pesan.update-qty');
    Route::get('/kasir/pesan/clear', 'clearCart')->name('kasir.pesan.clear');
    
    // Pastikan ini di luar prefix 'admin' dan grup 'Profile'
    Route::get('/kasir/pesan/print/{id}', 'printStruk')->name('kasir.pesan.print');
    Route::get('/kasir/riwayat', 'index')->name('kasir.riwayat.index');
    Route::get('/kasir/profile', [ProfileController::class, 'index'])->name('kasir.profile.index');
    Route::put('/kasir/profile/update', [ProfileController::class, 'update'])->name('kasir.profile.update');
    });
    
// --- Route Login & Auth ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    

    // --- Route Grup Admin ---
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('cabang', CabangController::class);
        Route::resource('menu', MenuController::class);
        Route::resource('user', UserController::class)->parameters(['user' => 'id']);
        Route::resource('toping', ToppingController::class);
        Route::resource('pembelian', BeliBahanController::class);
        Route::get('/target', [TargetController::class, 'index'])->name('target.index');
        Route::post('/target/store', [TargetController::class, 'store'])->name('target.store');
        Route::get('/laporantarget', [LaporanTargetController::class, 'index'])->name('laporantarget.index');

        // Route Profile (Hanya untuk urusan Profil)
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'index')->name('profile.index');
            Route::put('/profile/update', 'update')->name('profile.update');
            Route::put('/profile/password', 'updatePassword')->name('profile.password');
        });
    });
    //laporan
    // Tambahkan di dalam file routes/web.php
Route::get('/admin/laporan/harian', function () {
    return view('admin.laporan.harian');
})->name('admin.laporan.harian');
Route::get('/admin/laporan/bulanan', function () {
    return view('admin.laporan.bulanan');
})->name('admin.laporan.bulanan');
Route::get('/admin/laporan/tahunan', function () {
    return view('admin.laporan.tahunan');
})->name('admin.laporan.tahunan');

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
});