<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Paksa HTTPS di production (Railway berada di belakang proxy)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        \Illuminate\Pagination\Paginator::useTailwind();

        // Bagikan data daftar cabang hanya 1 kali per request untuk efisiensi performa
        View::composer(['components.admin.navbar', 'components.kasir.navbar', 'admin.*'], function ($view) {
            static $daftarCabang = null;
            if ($daftarCabang === null) {
                try {
                    $daftarCabang = DB::table('tb_cabang')->where('is_active', 1)->orderBy('nama_cabang')->get();
                } catch (\Throwable $e) {
                    $daftarCabang = collect();
                }
            }
            $view->with('daftarCabang', $daftarCabang);
        });
    }
}