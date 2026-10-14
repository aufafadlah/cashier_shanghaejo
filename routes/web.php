<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{CabangController, MenuController, UserController, BeliBahanController, ToppingController, ProfileController, LoginController, LaporanController, DashboardController, DeletionLogController, SettingController};
use App\Http\Controllers\Kasir\PesanKasirController;

// ⚠️ DEBUG ROUTE — HAPUS SETELAH MASALAH TERIDENTIFIKASI
Route::get('/debug-info', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbStatus = 'OK - Connected to: ' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        $dbStatus = 'FAILED: ' . $e->getMessage();
    }

    try {
        $sessionCount = \Illuminate\Support\Facades\DB::table('sessions')->count();
        $sessionStatus = "OK - {$sessionCount} sessions";
    } catch (\Exception $e) {
        $sessionStatus = 'FAILED: ' . $e->getMessage();
    }

    return response()->json([
        'app_env'          => config('app.env'),
        'app_url'          => config('app.url'),
        'app_debug'        => config('app.debug'),
        'db_connection'    => config('database.default'),
        'db_status'        => $dbStatus,
        'session_driver'   => config('session.driver'),
        'session_encrypt'  => config('session.encrypt'),
        'session_domain'   => config('session.domain'),
        'session_status'   => $sessionStatus,
        'php_version'      => PHP_VERSION,
        'csrf_token'       => csrf_token(),
    ]);
});

// 1. RUTE PUBLIK (Tanpa Login)
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// 2. RUTE DENGAN LOGIN (Autentikasi)
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // --- RUTE KASIR ---
    Route::middleware(['role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
        Route::controller(PesanKasirController::class)->group(function () {
            Route::get('/pesan', 'index')->name('pesan.index');
            Route::post('/pesan/add', 'addToCart')->name('pesan.add');
            Route::post('/pesan/checkout', 'checkout')->name('pesan.checkout');
            Route::post('/pesan/remove/{index}', 'removeCart')->name('pesan.remove');
            Route::post('/pesan/update-qty/{index}/{action}', 'updateQty')->name('pesan.update-qty');
            Route::post('/pesan/clear', 'clearCart')->name('pesan.clear');
            Route::get('/pesan/print/{id}', 'printStruk')->name('pesan.print');
            Route::get('/riwayat', 'riwayat')->name('riwayat.index');
            Route::get('/riwayat/export/excel', 'exportExcel')->name('riwayat.export.excel');
            Route::get('/riwayat/export/pdf', 'exportPdf')->name('riwayat.export.pdf');
            Route::delete('/riwayat/{id}', 'deleteOrder')->name('riwayat.delete');
        });
        // Profile Kasir
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // --- RUTE ADMIN (Gunakan middleware 'role:admin' di sini) ---
    // Set-cabang filter harus di luar AdminFilterMiddleware agar tidak di-override
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::post('/set-cabang-filter', function (\Illuminate\Http\Request $request) {
            // Simpan nilai baru ke session (null = Semua Cabang)
            $newValue = $request->input('id_cabang') !== '' ? $request->input('id_cabang') : null;
            session(['admin_cabang_filter' => $newValue]);
            return back()->with('success', 'Filter cabang diterapkan.');
        })->name('set.cabang');
    });

    Route::middleware(['role:admin', \App\Http\Middleware\AdminFilterMiddleware::class])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('cabang', CabangController::class);
        Route::resource('menu', MenuController::class);
        Route::resource('user', UserController::class)->parameters(['user' => 'id']);
        Route::resource('toping', ToppingController::class);
        Route::resource('pembelian', BeliBahanController::class);

        // Riwayat Penghapusan (Audit Log Kasir)
        Route::get('/riwayat-penghapusan', [DeletionLogController::class, 'index'])->name('deletion-log.index');
        Route::get('/riwayat-penghapusan/export/excel', [DeletionLogController::class, 'exportExcel'])->name('deletion-log.export.excel');

        // Laporan Penjualan & Export
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
        Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

        // Laporan Pengeluaran & Export/Import
        Route::get('/laporanpembelian', [BeliBahanController::class, 'laporan'])->name('laporanpembelian.index');
        Route::get('/laporanpembelian/export/excel', [BeliBahanController::class, 'exportExcel'])->name('laporanpembelian.export.excel');
        Route::get('/laporanpembelian/export/pdf', [BeliBahanController::class, 'exportPdf'])->name('laporanpembelian.export.pdf');
        Route::post('/laporanpembelian/import', [BeliBahanController::class, 'importExcel'])->name('laporanpembelian.import');

        // Profile Admin
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'index')->name('profile.index');
            Route::put('/profile/update', 'update')->name('profile.update');
            Route::put('/profile/password', 'updatePassword')->name('profile.password');
        });

        // Pengaturan Struk
        Route::get('/setting', [SettingController::class, 'index'])->name('setting.index');
        Route::put('/setting', [SettingController::class, 'update'])->name('setting.update');
    });
});