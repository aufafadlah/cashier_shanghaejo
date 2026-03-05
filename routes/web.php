<?php

use Illuminate\Support\Facades\Route; // Pastikan ini ada di paling atas
use App\Http\Controllers\Admin\CabangController;

Route::get('/', function () {
    return view('dashboard');
});
Route::prefix('admin')->group(function() {
    Route::resource('cabang', CabangController::class);
});
use App\Http\Controllers\Admin\MenuController;

// Pastikan prefix sesuai dengan yang kamu inginkan (misal: admin/menu)
Route::prefix('admin')->group(function () {
    Route::resource('menu', MenuController::class);
});