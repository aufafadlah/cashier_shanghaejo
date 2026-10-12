<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Admin: gunakan session filter. Kasir: kunci ke cabang sendiri.
        if ($user->role === 'admin') {
            $selectedCabang = session('admin_cabang_filter');
        } else {
            $selectedCabang = $user->id_cabang;
        }

        $hariIni    = Carbon::today()->toDateString();
        $weekStart  = Carbon::today()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd    = Carbon::today()->endOfWeek(Carbon::SUNDAY)->toDateString();

        // =========================================================
        // 1. STATS (Combined aggregate: 1 query instead of 3)
        // =========================================================
        $queryPenjualan = DB::table('tb_penjualan')
            ->where('status_transaksi', 'berhasil')
            ->whereDate('created_at', $hariIni);

        $queryPengeluaran = DB::table('tb_beli_bahan')
            ->whereDate('tgl', $hariIni);

        if ($selectedCabang) {
            $queryPenjualan->where('id_cabang', $selectedCabang);
            $queryPengeluaran->where('id_cabang', $selectedCabang);
        }

        // Single query for count + sum instead of 2 separate queries
        $aggPenjualan = (clone $queryPenjualan)
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(total_bayar), 0) as total_omzet')
            ->first();
        
        $totalOmzet     = $aggPenjualan->total_omzet;
        $countTransaksi = $aggPenjualan->total_count;
        $totalBeli      = $queryPengeluaran->sum('harga_total') ?? 0;
        $totalPelanggan = $countTransaksi;
        $countMenu      = DB::table('tb_menu')->where('is_active', 1)->count();

        // =========================================================
        // 2. GRAFIK MINGGUAN
        // =========================================================
        $queryMinggu = DB::table('tb_penjualan')
            ->where('status_transaksi', 'berhasil')
            ->whereDate('created_at', '>=', $weekStart)
            ->whereDate('created_at', '<=', $weekEnd);

        if ($selectedCabang) {
            $queryMinggu->where('id_cabang', $selectedCabang);
        }

        $grafikData = (clone $queryMinggu)
            ->select(DB::raw('DAYOFWEEK(created_at) AS dow'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(total_bayar) AS total_pendapatan'))
            ->groupBy(DB::raw('DAYOFWEEK(created_at)'))
            ->orderBy(DB::raw('DAYOFWEEK(created_at)'))
            ->get();

        // =========================================================
        // 3. METODE PEMBAYARAN
        // =========================================================
        $rawPie = (clone $queryPenjualan)
            ->select(DB::raw('UPPER(TRIM(metode_pembayaran)) AS metode'), DB::raw('COUNT(*) AS jumlah'))
            ->groupBy(DB::raw('UPPER(TRIM(metode_pembayaran))'))
            ->get();

        $metodePembayaran = [];
        foreach ($rawPie as $row) {
            $key = match (strtolower($row->metode)) {
                'qris'  => 'QRIS',
                'cash'  => 'Cash',
                default => ucfirst(strtolower($row->metode)),
            };
            $metodePembayaran[$key] = $row->jumlah;
        }

        $totalMetode = array_sum($metodePembayaran);
        $qrisPct = $totalMetode > 0 ? round(($metodePembayaran['QRIS'] ?? 0) / $totalMetode * 100) : 0;
        $cashPct = $totalMetode > 0 ? round(($metodePembayaran['Cash'] ?? 0) / $totalMetode * 100) : 0;

        // =========================================================
        // 4. TRANSAKSI TERBARU (6 terakhir)
        // =========================================================
        $queryTerbaru = DB::table('tb_penjualan')
            ->whereDate('created_at', $hariIni)
            ->orderBy('created_at', 'desc')
            ->limit(6);

        if ($selectedCabang) {
            $queryTerbaru->where('id_cabang', $selectedCabang);
        }
        $transaksi = $queryTerbaru->get();

        // =========================================================
        // 5. DISTRIBUSI CABANG
        // =========================================================
        $queryCabang = DB::table('tb_penjualan')
            ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->where('tb_penjualan.status_transaksi', 'berhasil')
            ->whereDate('tb_penjualan.created_at', $hariIni);

        if ($selectedCabang) {
            $queryCabang->where('tb_penjualan.id_cabang', $selectedCabang);
        }

        $cabangBreakdown = $queryCabang
            ->select(DB::raw('COALESCE(tb_cabang.nama_cabang, "Tanpa Cabang") AS nama_cabang'), DB::raw('SUM(tb_penjualan.total_bayar) AS total'))
            ->groupBy('tb_cabang.nama_cabang')
            ->orderByDesc('total')
            ->get();

        // daftarCabang sudah disediakan oleh AppServiceProvider via View Composer
        // Tidak perlu query ulang di sini

        return view('admin.dashboard', compact(
            'totalOmzet', 
            'countTransaksi', 
            'totalBeli', 
            'totalPelanggan',
            'countMenu', 
            'grafikData',
            'metodePembayaran',
            'qrisPct',
            'cashPct',
            'transaksi',
            'cabangBreakdown',
            'selectedCabang'
        ));
    }
}