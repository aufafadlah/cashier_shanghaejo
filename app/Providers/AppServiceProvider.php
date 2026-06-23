<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Bagikan data ke semua view
        View::composer('*', function ($view) {
            $view->with('daftarCabang', DB::table('tb_cabang')->get());
        });
    }
}
