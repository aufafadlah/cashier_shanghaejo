<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\MenuController;   // Punya Farida
use App\Http\Controllers\Admin\UserController;   // Punya Nabila
use App\Http\Controllers\Admin\TargetController; // Punya Kamu

// --- Halaman Utama ---
Route::get('/', function () {
    return view('home');
});

// --- Route Cabang ---
Route::prefix('admin')->group(function() {
    Route::resource('cabang', CabangController::class);
});

// --- Route Menu (Punya Farida) ---
Route::prefix('admin')->group(function () {
    // Route resource untuk CRUD Menu
    Route::resource('menu', MenuController::class);
});

// --- Route User (Punya Nabila) ---
Route::prefix('admin')->group(function () {
    // Route resource untuk CRUD User
    Route::resource('user', UserController::class)->parameters([
        'user' => 'id'
    ]);
});

// --- Route Target (Punya Kamu) ---
Route::prefix('admin')->name('admin.')->group(function () {
    // Route khusus untuk Kelola Target Harian
    Route::get('/target', [TargetController::class, 'index'])->name('target.index');
    Route::post('/target/store', [TargetController::class, 'store'])->name('target.store');
});