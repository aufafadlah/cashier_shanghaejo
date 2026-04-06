<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\MenuController;   // Punya Farida
use App\Http\Controllers\Admin\UserController;   // Punya Nabila
use App\Http\Controllers\Admin\TargetController; // Punya Kamu
use App\Http\Controllers\Admin\BeliBahanController;
use App\Http\Controllers\Admin\ToppingController; //punya frd
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\LoginController;

// Route Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Grouping Route yang perlu Login
Route::middleware(['auth'])->group(function () {
    
    // DASHBOARD ADMIN
    // Sesuai gambar, file kamu ada di admin/dashboard.blade.php
    Route::get('/admin/dashboard/index', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');

    // POS KASIR
    // Sesuai gambar, file kamu ada di kasir/pos.blade.php
    Route::get('/kasir/pos', function () {
        return view('kasir.pos');
    })->name('kasir.pos');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

    // --- Halaman Utama ---
    Route::get('/', function () {
        return view('home');
    });
    // --- Route Grup Admin (Gabungan Semua) ---
    Route::prefix('admin')->name('admin.')->group(function () {
    
    // Route Cabang (Sekarang otomatis jadi admin.cabang.index, admin.cabang.destroy, dll)
    Route::resource('cabang', CabangController::class);

    // Route Menu
    Route::resource('menu', MenuController::class);

    

    // Route User
    Route::resource('user', UserController::class)->parameters(['user' => 'id']);

    // Route Target
    Route::get('/target', [TargetController::class, 'index'])->name('target.index');
    Route::post('/target/store', [TargetController::class, 'store'])->name('target.store');

    // Route Pembelian
    // Ubah jadi simpel begini saja:
    Route::resource('pembelian', BeliBahanController::class);

    // Route Topping
    Route::resource('toping', ToppingController::class);

    // Route Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'index')->name('profile.index');
        Route::put('/profile/update', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });
});