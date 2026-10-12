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
        $id_cabang = ($user->role === 'kasir') ? $user->id_cabang : ($request->input('id_cabang') ?: session('admin_cabang_filter'));

        $daftarCabang = DB::table('tb_cabang')->orderBy('nama_cabang')->get();

        // ═══════════════════════════════════════════════════════════════
        // 1. HARIAN (Single Day) — Combined aggregate: 1 query instead of 3
        // ═══════════════════════════════════════════════════════════════
        $singleDate = $request->input('single_date', Carbon::today()->toDateString());
        $baseDay = function () use ($id_cabang, $singleDate) {
            $q = DB::table('tb_penjualan')->whereDate('tb_penjualan.created_at', $singleDate);
            if ($id_cabang) $q->where('tb_penjualan.id_cabang', $id_cabang);
            return $q;
        };
        $baseDayBerhasil = function () use ($baseDay) {
            return $baseDay()->where('tb_penjualan.status_transaksi', 'berhasil');
        };

        // Single aggregate query replaces 3 separate count/sum calls
        $aggDay = (clone $baseDayBerhasil())
            ->selectRaw('COUNT(*) as total_penjualan, COALESCE(SUM(tb_penjualan.total_bayar), 0) as total_pendapatan')
            ->first();

        $harianSingle = [
            'tanggal'         => $singleDate,
            'totalPenjualan'  => $aggDay->total_penjualan,
            'totalPendapatan' => $aggDay->total_pendapatan,
            'totalPelanggan'  => $aggDay->total_penjualan,
            'recentTransaksi' => (clone $baseDay())
                ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
                ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_penjualan.*', 'tb_user.name as nama_kasir', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_penjualan.created_at')
                ->paginate(10, ['*'], 'page_s')
                ->withQueryString()
                ->appends(['tab' => 'harian']),
            'metodePembayaran' => $this->getMetode(clone $baseDayBerhasil()),
        ];
        [$harianSingle['qrisPct'], $harianSingle['cashPct']] = $this->calcPct($harianSingle['metodePembayaran']);

        // ═══════════════════════════════════════════════════════════════
        // 2. MINGGUAN — Combined aggregate
        // ═══════════════════════════════════════════════════════════════
        $tanggalInput = $request->input('tanggal', Carbon::today()->toDateString());
        $tanggalRef   = Carbon::parse($tanggalInput);
        $weekStart    = $tanggalRef->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd      = $tanggalRef->copy()->endOfWeek(Carbon::SUNDAY);

        $baseH = function () use ($id_cabang, $weekStart, $weekEnd) {
            $q = DB::table('tb_penjualan')
                ->whereDate('tb_penjualan.created_at', '>=', $weekStart->toDateString())
                ->whereDate('tb_penjualan.created_at', '<=', $weekEnd->toDateString());
            if ($id_cabang) $q->where('tb_penjualan.id_cabang', $id_cabang);
            return $q;
        };
        $baseHBerhasil = function () use ($baseH) {
            return $baseH()->where('tb_penjualan.status_transaksi', 'berhasil');
        };

        $aggWeek = (clone $baseHBerhasil())
            ->selectRaw('COUNT(*) as total_penjualan, COALESCE(SUM(tb_penjualan.total_bayar), 0) as total_pendapatan')
            ->first();

        $harian = [
            'tanggal'         => $tanggalInput,
            'weekStart'       => $weekStart,
            'weekEnd'         => $weekEnd,
            'totalPenjualan'  => $aggWeek->total_penjualan,
            'totalPendapatan' => $aggWeek->total_pendapatan,
            'totalPelanggan'  => $aggWeek->total_penjualan,
            'recentTransaksi' => (clone $baseH())
                ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
                ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_penjualan.*', 'tb_user.name as nama_kasir', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_penjualan.created_at')
                ->paginate(10, ['*'], 'page_h')
                ->withQueryString()
                ->appends(['tab' => 'mingguan']),
            'grafikData'      => (function() use ($baseHBerhasil) {
                $isSqlite = DB::connection()->getDriverName() === 'sqlite';
                $dowExpr = $isSqlite ? "(CAST(strftime('%w', tb_penjualan.created_at) AS INTEGER) + 1)" : "DAYOFWEEK(tb_penjualan.created_at)";
                return (clone $baseHBerhasil())
                    ->select(DB::raw("{$dowExpr} AS dow"), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(tb_penjualan.total_bayar) AS total_pendapatan'))
                    ->groupBy(DB::raw($dowExpr))->orderBy(DB::raw($dowExpr))->get();
            })(),
            'metodePembayaran' => $this->getMetode(clone $baseHBerhasil()),
        ];
        [$harian['qrisPct'], $harian['cashPct']] = $this->calcPct($harian['metodePembayaran']);

        // ═══════════════════════════════════════════════════════════════
        // 3. BULANAN — Combined aggregate
        // ═══════════════════════════════════════════════════════════════
        $tahunBulanan = intval($request->input('tahun_bulanan', Carbon::today()->year));

        $baseB = function () use ($id_cabang, $tahunBulanan) {
            $q = DB::table('tb_penjualan')->whereYear('tb_penjualan.created_at', $tahunBulanan);
            if ($id_cabang) $q->where('tb_penjualan.id_cabang', $id_cabang);
            return $q;
        };
        $baseBBerhasil = function () use ($baseB) {
            return $baseB()->where('tb_penjualan.status_transaksi', 'berhasil');
        };

        $aggMonth = (clone $baseBBerhasil())
            ->selectRaw('COUNT(*) as total_penjualan, COALESCE(SUM(tb_penjualan.total_bayar), 0) as total_pendapatan')
            ->first();

        $bulanan = [
            'tahun'           => $tahunBulanan,
            'totalPenjualan'  => $aggMonth->total_penjualan,
            'totalPendapatan' => $aggMonth->total_pendapatan,
            'totalPelanggan'  => $aggMonth->total_penjualan,
            'recentTransaksi' => (clone $baseB())
                ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
                ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_penjualan.*', 'tb_user.name as nama_kasir', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_penjualan.created_at')
                ->paginate(10, ['*'], 'page_b')
                ->withQueryString()
                ->appends(['tab' => 'bulanan']),
            'grafikData'      => (function() use ($baseBBerhasil) {
                $isSqlite = DB::connection()->getDriverName() === 'sqlite';
                $monthExpr = $isSqlite ? "CAST(strftime('%m', tb_penjualan.created_at) AS INTEGER)" : "MONTH(tb_penjualan.created_at)";
                return (clone $baseBBerhasil())
                    ->select(DB::raw("{$monthExpr} AS bulan"), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(tb_penjualan.total_bayar) AS total_pendapatan'))
                    ->groupBy(DB::raw($monthExpr))->orderBy(DB::raw($monthExpr))->get();
            })(),
            'metodePembayaran' => $this->getMetode(clone $baseBBerhasil()),
        ];
        [$bulanan['qrisPct'], $bulanan['cashPct']] = $this->calcPct($bulanan['metodePembayaran']);

        // ═══════════════════════════════════════════════════════════════
        // 4. TAHUNAN — Combined aggregate
        // ═══════════════════════════════════════════════════════════════
        $tahunSekarang = intval(Carbon::today()->year);
        $tahunMulai    = $tahunSekarang - 4;

        $baseT = function () use ($id_cabang, $tahunMulai, $tahunSekarang) {
            $q = DB::table('tb_penjualan')
                ->whereYear('tb_penjualan.created_at', '>=', $tahunMulai)
                ->whereYear('tb_penjualan.created_at', '<=', $tahunSekarang);
            if ($id_cabang) $q->where('tb_penjualan.id_cabang', $id_cabang);
            return $q;
        };
        $baseTBerhasil = function () use ($baseT) {
            return $baseT()->where('tb_penjualan.status_transaksi', 'berhasil');
        };

        $aggYear = (clone $baseTBerhasil())
            ->selectRaw('COUNT(*) as total_penjualan, COALESCE(SUM(tb_penjualan.total_bayar), 0) as total_pendapatan')
            ->first();

        $tahunan = [
            'tahunMulai'      => $tahunMulai,
            'tahunSekarang'   => $tahunSekarang,
            'totalPenjualan'  => $aggYear->total_penjualan,
            'totalPendapatan' => $aggYear->total_pendapatan,
            'totalPelanggan'  => $aggYear->total_penjualan,
            'recentTransaksi' => (clone $baseT())
                ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
                ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_penjualan.*', 'tb_user.name as nama_kasir', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_penjualan.created_at')
                ->paginate(10, ['*'], 'page_t')
                ->withQueryString()
                ->appends(['tab' => 'tahunan']),
            'grafikData'      => (function() use ($baseTBerhasil) {
                $isSqlite = DB::connection()->getDriverName() === 'sqlite';
                $yearExpr = $isSqlite ? "CAST(strftime('%Y', tb_penjualan.created_at) AS INTEGER)" : "YEAR(tb_penjualan.created_at)";
                return (clone $baseTBerhasil())
                    ->select(DB::raw("{$yearExpr} AS tahun"), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(tb_penjualan.total_bayar) AS total_pendapatan'))
                    ->groupBy(DB::raw($yearExpr))->orderBy(DB::raw($yearExpr))->get();
            })(),
            'metodePembayaran' => $this->getMetode(clone $baseTBerhasil()),
        ];
        [$tahunan['qrisPct'], $tahunan['cashPct']] = $this->calcPct($tahunan['metodePembayaran']);

        return view('admin.laporan.index', compact(
            'isAdmin', 'id_cabang', 'daftarCabang',
            'harianSingle', 'harian', 'bulanan', 'tahunan'
        ));
    }

    private function resolveFilter(Request $request): array
    {
        Carbon::setLocale('id');
        $id_cabang = $request->input('id_cabang') ?: session('admin_cabang_filter');
        $tab = strtolower(trim((string)$request->input('tab', 'harian')));

        $query = DB::table('tb_penjualan')
            ->leftJoin('tb_user', 'tb_penjualan.id_user', '=', 'tb_user.id_user')
            ->leftJoin('tb_cabang', 'tb_penjualan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->select('tb_penjualan.*', 'tb_user.name as nama_kasir', 'tb_cabang.nama_cabang');

        if ($id_cabang) {
            $query->where('tb_penjualan.id_cabang', $id_cabang);
        }

        // Cek tab: mingguan jika tab mingguan/minggu atau jika tab harian tapi membawa tanggal dan bukan single_date
        $isMingguan = in_array($tab, ['mingguan', 'minggu']) || ($tab === 'harian' && $request->filled('tanggal') && !$request->filled('single_date'));

        if ($isMingguan) {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            $ref = Carbon::parse($tanggal);
            $weekStart = $ref->copy()->startOfWeek(Carbon::MONDAY);
            $weekEnd = $ref->copy()->endOfWeek(Carbon::SUNDAY);
            $query->whereDate('tb_penjualan.created_at', '>=', $weekStart->toDateString())
                  ->whereDate('tb_penjualan.created_at', '<=', $weekEnd->toDateString());
            $startText = $weekStart->translatedFormat('d M Y');
            $endText = $weekEnd->translatedFormat('d M Y');
            $periodeText = "Mingguan ({$startText} - {$endText})";
            $fileLabel = "MINGGUAN_" . $weekStart->format('Ymd') . "_" . $weekEnd->format('Ymd');
        } elseif ($tab === 'bulanan') {
            $tahun = intval($request->input('tahun_bulanan', Carbon::today()->year));
            $query->whereYear('tb_penjualan.created_at', $tahun);
            $periodeText = "Bulanan - Tahun " . $tahun;
            $fileLabel = "BULANAN_" . $tahun;
        } elseif ($tab === 'tahunan') {
            $tahunSekarang = intval(Carbon::today()->year);
            $tahunMulai = $tahunSekarang - 4;
            $query->whereYear('tb_penjualan.created_at', '>=', $tahunMulai)
                  ->whereYear('tb_penjualan.created_at', '<=', $tahunSekarang);
            $periodeText = "Tahunan ({$tahunMulai} - {$tahunSekarang})";
            $fileLabel = "TAHUNAN_{$tahunMulai}_{$tahunSekarang}";
        } else {
            // Harian (tab harian, single, atau default)
            $singleDate = $request->input('single_date', Carbon::today()->toDateString());
            $query->whereDate('tb_penjualan.created_at', $singleDate);
            $periodeText = "Harian - " . Carbon::parse($singleDate)->translatedFormat('l, d F Y');
            $fileLabel = "HARIAN_" . Carbon::parse($singleDate)->format('Ymd');
        }

        $transaksi = $query->orderByDesc('tb_penjualan.created_at')->get();
        $cabang = $id_cabang ? DB::table('tb_cabang')->where('id_cabang', $id_cabang)->first() : null;

        return compact('transaksi', 'periodeText', 'fileLabel', 'cabang', 'id_cabang', 'tab');
    }

    public function exportExcel(Request $request)
    {
        $filtered = $this->resolveFilter($request);
        $transaksi = $filtered['transaksi'];
        $periodeText = $filtered['periodeText'];
        $fileLabel = $filtered['fileLabel'];
        $cabang = $filtered['cabang'];

        $cabangSlug = $cabang ? '_' . str_replace(' ', '_', $cabang->nama_cabang) : '';
        $filename = "Laporan_Penjualan_{$fileLabel}{$cabangSlug}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($transaksi, $periodeText, $cabang) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel

            // Header metadata
            fputcsv($handle, ['LAPORAN PENJUALAN - SHANGHAEJO']);
            fputcsv($handle, ['Periode:', $periodeText]);
            fputcsv($handle, ['Cabang:', $cabang ? $cabang->nama_cabang : 'Semua Cabang']);
            fputcsv($handle, ['Waktu Export:', now()->translatedFormat('d F Y, H:i') . ' WIB']);
            fputcsv($handle, []);

            // Table Columns
            fputcsv($handle, [
                'No',
                'No. Struk',
                'Hari & Waktu',
                'Pelanggan',
                'Total Bayar (Rp)',
                'Metode Pembayaran',
                'Status',
                'Kasir',
                'Cabang'
            ]);

            $totalNominal = 0;
            $totalBerhasil = 0;

            foreach ($transaksi as $i => $row) {
                $isBerhasil = strtolower($row->status_transaksi ?? '') === 'berhasil';
                if ($isBerhasil) {
                    $totalNominal += $row->total_bayar;
                    $totalBerhasil++;
                }

                $noStruk = 'SHJ-' . date('dmy', strtotime($row->created_at)) . '-' . str_pad($row->id_penjualan, 4, '0', STR_PAD_LEFT);

                fputcsv($handle, [
                    $i + 1,
                    $noStruk,
                    date('d/m/Y H:i:s', strtotime($row->created_at)),
                    $row->nama_pelanggan ?? '-',
                    (int) $row->total_bayar,
                    strtoupper($row->metode_pembayaran ?? '-'),
                    strtoupper($row->status_transaksi ?? '-'),
                    $row->nama_kasir ?? '-',
                    $row->nama_cabang ?? '-'
                ]);
            }

            // Summary rows
            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', 'TOTAL TRANSAKSI', count($transaksi), '', '', '', '']);
            fputcsv($handle, ['', '', '', 'TRANSAKSI BERHASIL', $totalBerhasil, '', '', '', '']);
            fputcsv($handle, ['', '', '', 'TOTAL PENDAPATAN (BERHASIL)', (int) $totalNominal, '', '', '', '']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $filtered = $this->resolveFilter($request);
        $transaksi = $filtered['transaksi'];
        $periodeText = $filtered['periodeText'];
        $cabang = $filtered['cabang'];

        $totalNominal = $transaksi->where('status_transaksi', 'berhasil')->sum('total_bayar');
        $totalTransaksi = $transaksi->count();
        $totalBerhasil = $transaksi->where('status_transaksi', 'berhasil')->count();
        $totalGagal = $transaksi->where('status_transaksi', 'gagal')->count();

        $transaksiBerhasil = $transaksi->where('status_transaksi', 'berhasil');
        $nominalQris = $transaksiBerhasil->filter(fn($r) => strtolower($r->metode_pembayaran) === 'qris')->sum('total_bayar');
        $countQris = $transaksiBerhasil->filter(fn($r) => strtolower($r->metode_pembayaran) === 'qris')->count();
        $nominalCash = $transaksiBerhasil->filter(fn($r) => strtolower($r->metode_pembayaran) === 'cash')->sum('total_bayar');
        $countCash = $transaksiBerhasil->filter(fn($r) => strtolower($r->metode_pembayaran) === 'cash')->count();

        return view('admin.laporan.pdf', compact(
            'transaksi',
            'periodeText',
            'totalNominal',
            'totalTransaksi',
            'totalBerhasil',
            'totalGagal',
            'nominalQris',
            'countQris',
            'nominalCash',
            'countCash',
            'cabang'
        ));
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    private function getMetode($query): array
    {
        $raw = (clone $query)
            ->select(DB::raw('UPPER(TRIM(tb_penjualan.metode_pembayaran)) AS metode'), DB::raw('COUNT(*) AS jumlah'))
            ->groupBy(DB::raw('UPPER(TRIM(tb_penjualan.metode_pembayaran))'))->get();

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