<?php

use Illuminate\Support\Facades\Route; // Pastikan ini ada di paling atas
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('dashboard');
});
Route::prefix('admin')->group(function() {
    Route::resource('cabang', CabangController::class);
    Route::resource('user', UserController::class);
});
