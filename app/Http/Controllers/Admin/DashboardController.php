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
        
        // Memastikan string kosong dari URL (?id_cabang=) diubah menjadi null (Semua Cabang)
        if ($user->role === 'admin') {
            $selectedCabang = $request->filled('id_cabang') ? $request->get('id_cabang') : null; 
        } else {
            $selectedCabang = $user->id_cabang; // Kunci ke cabang dia sendiri
        }

        // AMBIL TANGGAL HARI INI SECARA DINAMIS (Menggunakan Tanggal dari Log: 2026-05-17)
        // Gunakan Carbon::today() agar jika hari ini tanggal 18, data 17 tidak terbawa.
        $hariIni = Carbon::today()->toDateString(); 

        // =========================================================
        // 1. QUERY BOX STATS (Hari Ini)
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

        $totalOmzet = $queryPenjualan->sum('total_bayar') ?? 0;
        $countTransaksi = $queryPenjualan->count();
        $totalBeli = $queryPengeluaran->sum('harga_total') ?? 0;
        $countMenu = DB::table('tb_menu')->where('is_active', 1)->count();


        // =========================================================
        // 2. QUERY GRAFIK HOURLY SALES (LINE CHART)
        // =========================================================
        $queryJam = DB::table('tb_penjualan')
            ->join('tb_detail_penjualan', 'tb_penjualan.id_penjualan', '=', 'tb_detail_penjualan.id_penjualan')
            ->select(
                DB::raw('HOUR(tb_penjualan.created_at) as jam'),
                DB::raw('SUM(tb_detail_penjualan.qty) as total_porsi'),
                DB::raw('SUM(tb_penjualan.total_bayar) as total_rupiah')
            )
            ->where('tb_penjualan.status_transaksi', 'berhasil')
            ->whereDate('tb_penjualan.created_at', $hariIni)
            ->groupBy(DB::raw('HOUR(tb_penjualan.created_at)'))
            ->orderBy('jam', 'asc');

        if ($selectedCabang) {
            $queryJam->where('tb_penjualan.id_cabang', $selectedCabang);
        }

        $rawChartData = $queryJam->get();

        // Siapkan array data rapi (Chart.js membutuhkan angka 0 agar grafik merender)
        $chartLabels = [];
        $chartPorsi = [];
        $chartRupiah = [];

        // Looping jam operasional sesuai template (Jam 10 pagi - 8 malam)
        for ($i = 10; $i <= 20; $i += 2) {
            $jamString = sprintf('%02d:00', $i);
            $chartLabels[] = $jamString;

            // Cari apakah di database ada data pada jam ini
            $match = $rawChartData->firstWhere('jam', $i);
            
            // Jika ada data porsi (int), masukkan. Jika tidak, isi 0 (wajib!).
            $chartPorsi[] = $match ? (int)$match->total_porsi : 0;
            $chartRupiah[] = $match ? (int)$match->total_rupiah : 0;
        }


        // =========================================================
        // 3. QUERY METODE PEMBAYARAN (DONUT CHART)
        // =========================================================
        $queryPie = DB::table('tb_penjualan')
            ->select('metode_pembayaran', DB::raw('count(*) as total'))
            ->where('status_transaksi', 'berhasil')
            ->whereDate('created_at', $hariIni)
            ->groupBy('metode_pembayaran');

        if ($selectedCabang) {
            $queryPie->where('id_cabang', $selectedCabang);
        }

        $rawPieData = $queryPie->get();
        $totalTransactionsPie = $rawPieData->sum('total');

        // Hitung persen QRIS vs CASH
        $qrisCount = $rawPieData->where('metode_pembayaran', 'qris')->first()->total ?? 0;
        $cashCount = $rawPieData->where('metode_pembayaran', 'cash')->first()->total ?? 0;

        $pieData = [
            'qris' => $totalTransactionsPie > 0 ? round(($qrisCount / $totalTransactionsPie) * 100) : 0,
            'cash' => $totalTransactionsPie > 0 ? round(($cashCount / $totalTransactionsPie) * 100) : 0,
        ];

        // Jika data pembayaran kosong total, beri default 0% agar donut chart tidak pecah
        if ($pieData['qris'] == 0 && $pieData['cash'] == 0) {
            $pieData = ['qris' => 0, 'cash' => 0];
        }


        // =========================================================
        // 4. QUERY TARGET MENU PROGRESS BAR
        // =========================================================
        $queryTarget = DB::table('tb_target')
            ->join('tb_menu', 'tb_target.id_menu', '=', 'tb_menu.id_menu')
            ->select('tb_target.*', 'tb_menu.nama_menu')
            ->whereDate('tb_target.tanggal', $hariIni);

        if ($selectedCabang) {
            $queryTarget->where('tb_target.id_cabang', $selectedCabang);
        }

        $targetMenu = $queryTarget->get()->map(function($item) {
            // Hitung persen pencapaian target (Aman pembagian 0)
            $item->persen = $item->stok_target > 0 ? round(($item->terjual / $item->stok_target) * 100) : 0;
            return $item;
        });


        // =========================================================
        // 5. QUERY LIST TRANSAKSI TERBARU (6 DATA TERAKHIR)
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
        // 6. QUERY DAFTAR CABANG UNTUK DROPDOWN
        // =========================================================
        $daftarCabang = DB::table('tb_cabang')->get();

        // Return semua variabel ke view blade (Pastikan tidak ada variabel tertinggal)
        return view('admin.dashboard', compact(
            'totalOmzet', 
            'countTransaksi', 
            'totalBeli', 
            'countMenu', 
            'chartLabels',
            'chartPorsi',
            'chartRupiah',
            'pieData',
            'targetMenu',
            'transaksi',
            'daftarCabang',
            'selectedCabang'
        ));
    }
}