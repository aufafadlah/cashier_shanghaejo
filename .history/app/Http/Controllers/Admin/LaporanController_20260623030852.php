<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanController extends Controller
{
public function harian(Request $request)
{
    $user = Auth::user();
    
    // --- TAMBAHKAN BARIS INI ---
    $isAdmin = ($user->role === 'admin'); 
    // ---------------------------
    
    // LOGIKA DINAMIS: Jika admin, ikuti pilihan dari Navbar (Request)
    if ($user->id_cabang) {
        $id_cabang = $user->id_cabang;
    } else {
        $id_cabang = $request->input('id_cabang'); 
    }

    // ... sisa kode Anda di bawahnya tetap sama ...
    // ... sisa kode di bawahnya biarkan tetap sama
        // ── 2. TENTUKAN TANGGAL ─────────────────────────────────────────
        // ?tanggal=2026-06-19  → lihat hari itu
        // (kosong)             → hari ini
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->toDateString()
            : Carbon::today()->toDateString();

        // ── 3. BASE QUERY HELPER ────────────────────────────────────────
        $base = function () use ($id_cabang, $tanggal) {
            $q = DB::table('tb_penjualan')
                ->whereDate('created_at', $tanggal);

            if ($id_cabang) {
                $q->where('id_cabang', $id_cabang);
            }

            return $q;
        };

        // ── 4. STATISTIK KARTU ──────────────────────────────────────────
        $totalPenjualan  = (clone $base())->count();
        $totalPendapatan = (clone $base())->sum('total_bayar');
        $totalPelanggan  = (clone $base())->distinct('nama_pelanggan')->count('nama_pelanggan');

        // ── 5. GRAFIK PER JAM ───────────────────────────────────────────
        // orderBy jam agar 00→23 berurutan
        $grafikData = (clone $base())
            ->select(
                DB::raw('HOUR(created_at)    AS jam'),
                DB::raw('COUNT(*)            AS total_transaksi'),
                DB::raw('SUM(total_bayar)    AS total_pendapatan')
            )
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy(DB::raw('HOUR(created_at)'))   // ← FIX: jam teratur
            ->get();

        // ── 6. METODE PEMBAYARAN ────────────────────────────────────────
        $metodePembayaranRaw = (clone $base())
            ->select(
                DB::raw('UPPER(TRIM(metode_pembayaran)) AS metode'),
                DB::raw('COUNT(*) AS jumlah')
            )
            ->groupBy(DB::raw('UPPER(TRIM(metode_pembayaran))'))
            ->get();

        $metodePembayaran = [];
        foreach ($metodePembayaranRaw as $row) {
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

        // ── 7. RECENT TRANSAKSI (tabel) ─────────────────────────────────
        $recentTransaksi = (clone $base())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // ── 8. DAFTAR CABANG (untuk dropdown admin) ─────────────────────
        $daftarCabang = DB::table('tb_cabang')->orderBy('nama_cabang')->get();
        $qsExtra = $request->only(['id_cabang']);
        

        
        // ── 9. KIRIM KE VIEW ────────────────────────────────────────────
        return view('admin.laporan.hn', compact(
            'totalPenjualan',
            'totalPendapatan',
            'totalPelanggan',
            'grafikData',
            'metodePembayaran',
            'qrisPct',
            'cashPct',
            'recentTransaksi',
            'tanggal',
            'id_cabang',
            'daftarCabang',
            'qsExtra',
            'isAdmin'
        ));
    }
}