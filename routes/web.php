<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\MenuController;   // Punya Farida
use App\Http\Controllers\Admin\UserController;   // Punya Nabila
use App\Http\Controllers\Admin\ToppingController; //punya frd

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
    Route::resource('menu', MenuController::class);
});

// --- Route User (Punya Nabila) ---
Route::prefix('admin')->group(function () {
    // Route resource untuk CRUD User
    Route::resource('user', UserController::class)->parameters([
        'user' => 'id'
    ]);
});

Route::prefix('admin')->group(function () {
    Route::resource('toping', ToppingController::class);
});