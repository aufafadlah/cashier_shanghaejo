<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanController extends Controller
{
    /** Semua route laporan diarahkan ke satu index() */
    public function harian(Request $request)  { return $this->index($request); }
    public function bulanan(Request $request) { return $this->index($request); }
    public function tahunan(Request $request) { return $this->index($request); }

    public function index(Request $request)
    {
        $user      = Auth::user();
        $isAdmin   = ($user->role === 'admin');
        $id_cabang = $user->id_cabang ?: $request->input('id_cabang');

        $daftarCabang = DB::table('tb_cabang')->orderBy('nama_cabang')->get();

        // ═══════════════════════════════════════════════════════════════
        // 1. HARIAN
        // ═══════════════════════════════════════════════════════════════
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->toDateString()
            : Carbon::today()->toDateString();

        $baseH = function () use ($id_cabang, $tanggal) {
            $q = DB::table('tb_penjualan')->whereDate('created_at', $tanggal);
            if ($id_cabang) $q->where('id_cabang', $id_cabang);
            return $q;
        };

        $baseHBerhasil = function () use ($baseH) {
            return $baseH()->where('status_transaksi', 'berhasil');
        };

        $harian = [
            'tanggal'          => $tanggal,
            'totalPenjualan'   => (clone $baseHBerhasil())->count(),
            'totalPendapatan'  => (clone $baseHBerhasil())->sum('total_bayar') ?? 0,
            'totalPelanggan'   => (clone $baseHBerhasil())->distinct('nama_pelanggan')->count('nama_pelanggan'),
            'recentTransaksi'  => (clone $baseH())->orderByDesc('created_at')->limit(10)->get(),
            'grafikData'       => (clone $baseHBerhasil())
                ->select(DB::raw('HOUR(created_at) AS jam'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(total_bayar) AS total_pendapatan'))
                ->groupBy(DB::raw('HOUR(created_at)'))->orderBy(DB::raw('HOUR(created_at)'))->get(),
            'metodePembayaran' => $this->getMetode(clone $baseHBerhasil()),
        ];
        [$harian['qrisPct'], $harian['cashPct']] = $this->calcPct($harian['metodePembayaran']);

        // ═══════════════════════════════════════════════════════════════
        // 2. BULANAN
        // ═══════════════════════════════════════════════════════════════
        $bulanInput  = $request->input('bulan');
        $monthCarbon = $bulanInput
            ? Carbon::parse($bulanInput . '-01')->startOfMonth()
            : Carbon::today()->startOfMonth();
        $bulanStr = $monthCarbon->format('Y-m');

        $baseB = function () use ($id_cabang, $monthCarbon) {
            $q = DB::table('tb_penjualan')
                ->whereYear('created_at', $monthCarbon->year)
                ->whereMonth('created_at', $monthCarbon->month);
            if ($id_cabang) $q->where('id_cabang', $id_cabang);
            return $q;
        };

        $baseBBerhasil = function () use ($baseB) {
            return $baseB()->where('status_transaksi', 'berhasil');
        };

        $bulanan = [
            'bulanStr'         => $bulanStr,
            'monthCarbon'      => $monthCarbon,
            'totalPenjualan'   => (clone $baseBBerhasil())->count(),
            'totalPendapatan'  => (clone $baseBBerhasil())->sum('total_bayar') ?? 0,
            'totalPelanggan'   => (clone $baseBBerhasil())->distinct('nama_pelanggan')->count('nama_pelanggan'),
            'recentTransaksi'  => (clone $baseB())->orderByDesc('created_at')->limit(10)->get(),
            'grafikData'       => (clone $baseBBerhasil())
                ->select(DB::raw('DAY(created_at) AS hari'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(total_bayar) AS total_pendapatan'))
                ->groupBy(DB::raw('DAY(created_at)'))->orderBy(DB::raw('DAY(created_at)'))->get(),
            'metodePembayaran' => $this->getMetode(clone $baseBBerhasil()),
        ];
        [$bulanan['qrisPct'], $bulanan['cashPct']] = $this->calcPct($bulanan['metodePembayaran']);

        // ═══════════════════════════════════════════════════════════════
        // 3. TAHUNAN
        // ═══════════════════════════════════════════════════════════════
        $tahun = intval($request->input('tahun', Carbon::today()->year));

        $baseT = function () use ($id_cabang, $tahun) {
            $q = DB::table('tb_penjualan')->whereYear('created_at', $tahun);
            if ($id_cabang) $q->where('id_cabang', $id_cabang);
            return $q;
        };

        $baseTBerhasil = function () use ($baseT) {
            return $baseT()->where('status_transaksi', 'berhasil');
        };

        $tahunan = [
            'tahun'            => $tahun,
            'totalPenjualan'   => (clone $baseTBerhasil())->count(),
            'totalPendapatan'  => (clone $baseTBerhasil())->sum('total_bayar') ?? 0,
            'totalPelanggan'   => (clone $baseTBerhasil())->distinct('nama_pelanggan')->count('nama_pelanggan'),
            'recentTransaksi'  => (clone $baseT())->orderByDesc('created_at')->limit(10)->get(),
            'grafikData'       => (clone $baseTBerhasil())
                ->select(DB::raw('MONTH(created_at) AS bulan'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(total_bayar) AS total_pendapatan'))
                ->groupBy(DB::raw('MONTH(created_at)'))->orderBy(DB::raw('MONTH(created_at)'))->get(),
            'metodePembayaran' => $this->getMetode(clone $baseTBerhasil()),
        ];
        [$tahunan['qrisPct'], $tahunan['cashPct']] = $this->calcPct($tahunan['metodePembayaran']);

        return view('admin.laporan.index', compact(
            'isAdmin', 'id_cabang', 'daftarCabang',
            'harian', 'bulanan', 'tahunan'
        ));
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    private function getMetode($query): array
    {
        $raw = (clone $query)
            ->select(DB::raw('UPPER(TRIM(metode_pembayaran)) AS metode'), DB::raw('COUNT(*) AS jumlah'))
            ->groupBy(DB::raw('UPPER(TRIM(metode_pembayaran))'))->get();

        $result = [];
        foreach ($raw as $row) {
            $key = match (strtolower($row->metode)) {
                'qris'  => 'QRIS',
                'cash'  => 'Cash',
                default => ucfirst(strtolower($row->metode)),
            };
            $result[$key] = $row->jumlah;
        }
        return $result;
    }

    private function calcPct(array $metode): array
    {
        $total   = array_sum($metode);
        $qrisPct = $total > 0 ? round(($metode['QRIS'] ?? 0) / $total * 100) : 0;
        $cashPct = $total > 0 ? round(($metode['Cash'] ?? 0) / $total * 100) : 0;
        return [$qrisPct, $cashPct];
    }
}