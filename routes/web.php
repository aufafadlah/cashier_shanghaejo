<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BeliBahanController;
use App\Http\Controllers\Admin\ProfileController;


// --- Halaman Utama ---
Route::get('/', function () {
    return view('home');
});

// --- Semua Route Admin (Hanya 1 Grup Saja) ---
Route::prefix('admin')->group(function () {
    
    // 1. Route Cabang
    Route::resource('cabang', CabangController::class);

    // 2. Route Menu (Punya Farida)
    Route::resource('menu', MenuController::class);

    // 3. Route User (Punya Nabila)
    Route::resource('user', UserController::class)->parameters([
        'user' => 'id'
    ]);

    // 4. Route Pembelian / Pengeluaran
    Route::resource('pembelian', BeliBahanController::class)->parameters([
        'pembelian' => 'id'
    ]);

    Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'index')->name('profile.index');
    Route::put('/profile/update', 'update')->name('profile.update');
    Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

}); // <--- Ini adalah penutup baris 31 yang benar