<?php

use Illuminate\Support\Facades\Route; // Pastikan ini ada di paling atas
use App\Http\Controllers\Admin\CabangController;
use App\Models\Cabang;

Route::get('/', function () {
    return view('home');
});
Route::prefix('admin')->group(function() {
    Route::resource('cabang', CabangController::class);
});
<<<<<<< HEAD
use App\Http\Controllers\Admin\MenuController;

// Pastikan prefix sesuai dengan yang kamu inginkan (misal: admin/menu)
Route::prefix('admin')->group(function () {
    Route::resource('menu', MenuController::class);
=======
Route::prefix('admin')->group(function() {
    Route::resource('cabang', CabangController::class);
>>>>>>> 528ea2f4e30f0e2af101d2754f4ed68e6c1a6ed6
});