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
        $totalPelanggan = (clone $queryPenjualan)->distinct('nama_pelanggan')->count('nama_pelanggan');
        $countMenu = DB::table('tb_menu')->where('is_active', 1)->count();


        // =========================================================
        // 2. QUERY GRAFIK HOURLY SALES (ApexCharts format)
        // =========================================================
        $grafikData = (clone $queryPenjualan)
            ->select(DB::raw('HOUR(created_at) AS jam'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(total_bayar) AS total_pendapatan'))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy(DB::raw('HOUR(created_at)'))
            ->get();


        // =========================================================
        // 3. QUERY METODE PEMBAYARAN (ApexCharts format)
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
            'totalPelanggan',
            'countMenu', 
            'grafikData',
            'metodePembayaran',
            'qrisPct',
            'cashPct',
            'targetMenu',
            'transaksi',
            'daftarCabang',
            'selectedCabang'
        ));
    }
}