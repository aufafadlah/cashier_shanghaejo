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
Route::prefix('admin')->group(function() {
    Route::resource('cabang', CabangController::class);
});