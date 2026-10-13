<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeliBahan;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeliBahanController extends Controller
{
public function index(Request $request)
{
    // 1. Mulai query dengan eager loading relasi 'cabang'
    $query = BeliBahan::with('cabang')->latest();

    // 2. Tambahkan filter jika ada parameter id_cabang di URL
    if ($request->has('id_cabang') && !empty($request->id_cabang)) {
        $query->where('id_cabang', $request->id_cabang);
    }

    // 3. Tambahkan filter search
    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('nama_barang', 'like', "%{$search}%")
              ->orWhere('supplier', 'like', "%{$search}%");
        });
    }

    // 4. Ambil data dengan pagination
    $pengeluaran = $query->paginate(10)->withQueryString();

    return view('admin.pembelian.index', compact('pengeluaran'));
}
    public function create()
    {
        $cabang = Cabang::all();
        return view('admin.pembelian.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        if ($request->has('harga_satuan')) {
            $request->merge([
                'harga_satuan' => $this->cleanCurrency($request->harga_satuan)
            ]);
        }

        $request->validate([
            'id_cabang'    => 'required',
            'nama_barang'  => 'required|string|max:255',
            'qty'          => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tgl'          => 'required|date',
            'supplier'     => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        // ✅ harga_total dihitung di satu tempat saja
        BeliBahan::create([
            'id_cabang'    => $request->id_cabang,
            'nama_barang'  => $request->nama_barang,
            'qty'          => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'harga_total'  => $this->hitungTotal($request->qty, $request->harga_satuan),
            'tgl'          => $request->tgl,
            'keterangan'   => $request->keterangan,
            'supplier'     => $request->supplier,
        ]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Pengeluaran berhasil ditambah');
    }

    public function edit($id)
    {
        $pengeluaran = BeliBahan::findOrFail($id);
        $cabang      = Cabang::all();
        return view('admin.pembelian.edit', compact('pengeluaran', 'cabang'));
    }

    public function update(Request $request, $id)
    {
        if ($request->has('harga_satuan')) {
            $request->merge([
                'harga_satuan' => $this->cleanCurrency($request->harga_satuan)
            ]);
        }

        $request->validate([
            'id_cabang'    => 'required',
            'nama_barang'  => 'required|string|max:255',
            'qty'          => 'required|numeric|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tgl'          => 'required|date',
            'supplier'     => 'required|string|max:100',
            'keterangan'   => 'nullable|string',
        ]);

        $data = BeliBahan::findOrFail($id);

        // ✅ harga_total dihitung lewat method yang sama
        $data->update([
            'id_cabang'    => $request->id_cabang,
            'nama_barang'  => $request->nama_barang,
            'qty'          => $request->qty,
            'harga_satuan' => $request->harga_satuan,
            'harga_total'  => $this->hitungTotal($request->qty, $request->harga_satuan),
            'tgl'          => $request->tgl,
            'keterangan'   => $request->keterangan,
            'supplier'     => $request->supplier,
        ]);

        return redirect()->route('admin.pembelian.index')->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $data = BeliBahan::findOrFail($id);
        $data->delete();
        return redirect()->route('admin.pembelian.index')->with('success', 'Data pengeluaran berhasil dihapus!');
    }

    public function laporan(Request $request)
    {
        $user      = auth()->user();
        $isAdmin   = ($user->role === 'admin');
        // Kasir: selalu kunci ke cabang sendiri. Admin: gunakan filter dari session atau query.
        $id_cabang = ($user->role === 'kasir') ? $user->id_cabang : ($request->input('id_cabang') ?: session('admin_cabang_filter'));

        $daftarCabang = DB::table('tb_cabang')->orderBy('nama_cabang')->get();

        // ═══════════════════════════════════════════════════════════════
        // 0. HARIAN SINGLE (Per Hari)
        // ═══════════════════════════════════════════════════════════════
        $singleDate = $request->input('single_date', Carbon::today()->toDateString());
        $baseDay = function () use ($id_cabang, $singleDate) {
            $q = DB::table('tb_beli_bahan')->whereDate('tb_beli_bahan.tgl', $singleDate);
            if ($id_cabang) $q->where('tb_beli_bahan.id_cabang', $id_cabang);
            return $q;
        };

        $harianSingle = [
            'tanggal'          => $singleDate,
            'totalTransaksi'   => (clone $baseDay())->count(),
            'totalPengeluaran' => (clone $baseDay())->sum('tb_beli_bahan.harga_total') ?? 0,
            'totalItem'        => (clone $baseDay())->sum('tb_beli_bahan.qty') ?? 0,
            'recentTransaksi'  => (clone $baseDay())
                ->leftJoin('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_beli_bahan.tgl')->orderByDesc('tb_beli_bahan.id_pengeluaran')->paginate(10, ['*'], 'page_s')->withQueryString()->appends(['tab' => 'harian']),
            'cabangBreakdown'  => $this->getCabangBreakdown(clone $baseDay()),
        ];

        // ═══════════════════════════════════════════════════════════════
        // 1. MINGGUAN → Data per minggu, dikelompokkan per hari (Sen-Min)
        // ═══════════════════════════════════════════════════════════════
        $tanggalInput = $request->input('tanggal', Carbon::today()->toDateString());
        $tanggalRef   = Carbon::parse($tanggalInput);
        $weekStart    = $tanggalRef->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd      = $tanggalRef->copy()->endOfWeek(Carbon::SUNDAY);

        $baseH = function () use ($id_cabang, $weekStart, $weekEnd) {
            $q = DB::table('tb_beli_bahan')
                ->whereDate('tb_beli_bahan.tgl', '>=', $weekStart->toDateString())
                ->whereDate('tb_beli_bahan.tgl', '<=', $weekEnd->toDateString());
            if ($id_cabang) $q->where('tb_beli_bahan.id_cabang', $id_cabang);
            return $q;
        };

        $harian = [
            'tanggal'          => $tanggalInput,
            'weekStart'        => $weekStart,
            'weekEnd'          => $weekEnd,
            'totalTransaksi'   => (clone $baseH())->count(),
            'totalPengeluaran' => (clone $baseH())->sum('tb_beli_bahan.harga_total') ?? 0,
            'totalItem'        => (clone $baseH())->sum('tb_beli_bahan.qty') ?? 0,
            'recentTransaksi'  => (clone $baseH())
                ->leftJoin('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_beli_bahan.tgl')->orderByDesc('tb_beli_bahan.id_pengeluaran')->paginate(10, ['*'], 'page_h')->withQueryString()->appends(['tab' => 'mingguan']),
            'grafikData'       => (clone $baseH())
                ->select(DB::raw('DAYOFWEEK(tb_beli_bahan.tgl) AS dow'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(tb_beli_bahan.harga_total) AS total_pengeluaran'))
                ->groupBy(DB::raw('DAYOFWEEK(tb_beli_bahan.tgl)'))->orderBy(DB::raw('DAYOFWEEK(tb_beli_bahan.tgl)'))->get(),
            'cabangBreakdown'  => $this->getCabangBreakdown(clone $baseH()),
        ];

        // ═══════════════════════════════════════════════════════════════
        // 2. BULANAN → Data per tahun, dikelompokkan per bulan (Jan-Des)
        // ═══════════════════════════════════════════════════════════════
        $tahunBulanan = intval($request->input('tahun_bulanan', Carbon::today()->year));

        $baseB = function () use ($id_cabang, $tahunBulanan) {
            $q = DB::table('tb_beli_bahan')->whereYear('tb_beli_bahan.tgl', $tahunBulanan);
            if ($id_cabang) $q->where('tb_beli_bahan.id_cabang', $id_cabang);
            return $q;
        };

        $bulanan = [
            'tahun'            => $tahunBulanan,
            'totalTransaksi'   => (clone $baseB())->count(),
            'totalPengeluaran' => (clone $baseB())->sum('tb_beli_bahan.harga_total') ?? 0,
            'totalItem'        => (clone $baseB())->sum('tb_beli_bahan.qty') ?? 0,
            'recentTransaksi'  => (clone $baseB())
                ->leftJoin('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_beli_bahan.tgl')->orderByDesc('tb_beli_bahan.id_pengeluaran')->paginate(10, ['*'], 'page_b')->withQueryString()->appends(['tab' => 'bulanan']),
            'grafikData'       => (clone $baseB())
                ->select(DB::raw('MONTH(tb_beli_bahan.tgl) AS bulan'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(tb_beli_bahan.harga_total) AS total_pengeluaran'))
                ->groupBy(DB::raw('MONTH(tb_beli_bahan.tgl)'))->orderBy(DB::raw('MONTH(tb_beli_bahan.tgl)'))->get(),
            'cabangBreakdown'  => $this->getCabangBreakdown(clone $baseB()),
        ];

        // ═══════════════════════════════════════════════════════════════
        // 3. TAHUNAN → Data lintas beberapa tahun, dikelompokkan per tahun
        // ═══════════════════════════════════════════════════════════════
        $tahunSekarang = intval(Carbon::today()->year);
        $tahunMulai    = $tahunSekarang - 4; // 5 tahun terakhir

        $baseT = function () use ($id_cabang, $tahunMulai, $tahunSekarang) {
            $q = DB::table('tb_beli_bahan')
                ->whereYear('tb_beli_bahan.tgl', '>=', $tahunMulai)
                ->whereYear('tb_beli_bahan.tgl', '<=', $tahunSekarang);
            if ($id_cabang) $q->where('tb_beli_bahan.id_cabang', $id_cabang);
            return $q;
        };

        $tahunan = [
            'tahunMulai'       => $tahunMulai,
            'tahunSekarang'    => $tahunSekarang,
            'totalTransaksi'   => (clone $baseT())->count(),
            'totalPengeluaran' => (clone $baseT())->sum('tb_beli_bahan.harga_total') ?? 0,
            'totalItem'        => (clone $baseT())->sum('tb_beli_bahan.qty') ?? 0,
            'recentTransaksi'  => (clone $baseT())
                ->leftJoin('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_beli_bahan.tgl')->orderByDesc('tb_beli_bahan.id_pengeluaran')->paginate(10, ['*'], 'page_t')->withQueryString()->appends(['tab' => 'tahunan']),
            'grafikData'       => (clone $baseT())
                ->select(DB::raw('YEAR(tb_beli_bahan.tgl) AS tahun'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(tb_beli_bahan.harga_total) AS total_pengeluaran'))
                ->groupBy(DB::raw('YEAR(tb_beli_bahan.tgl)'))->orderBy(DB::raw('YEAR(tb_beli_bahan.tgl)'))->get(),
            'cabangBreakdown'  => $this->getCabangBreakdown(clone $baseT()),
        ];

        return view('admin.laporanpembelian.index', compact(
            'isAdmin', 'id_cabang', 'daftarCabang',
            'harianSingle', 'harian', 'bulanan', 'tahunan'
        ));
    }

    private function resolveFilter(Request $request): array
    {
        Carbon::setLocale('id');
        $id_cabang = $request->input('id_cabang') ?: session('admin_cabang_filter');
        $tab = strtolower(trim((string)$request->input('tab', 'harian')));

        $query = DB::table('tb_beli_bahan')
            ->leftJoin('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang');

        if ($id_cabang) {
            $query->where('tb_beli_bahan.id_cabang', $id_cabang);
        }

        $isMingguan = in_array($tab, ['mingguan', 'minggu']) || ($tab === 'harian' && $request->filled('tanggal') && !$request->filled('single_date'));

        if ($isMingguan) {
            $tanggal = $request->input('tanggal', Carbon::today()->toDateString());
            $ref = Carbon::parse($tanggal);
            $weekStart = $ref->copy()->startOfWeek(Carbon::MONDAY);
            $weekEnd = $ref->copy()->endOfWeek(Carbon::SUNDAY);
            $query->whereDate('tb_beli_bahan.tgl', '>=', $weekStart->toDateString())
                  ->whereDate('tb_beli_bahan.tgl', '<=', $weekEnd->toDateString());
            $startText = $weekStart->translatedFormat('d M Y');
            $endText = $weekEnd->translatedFormat('d M Y');
            $periodeText = "Mingguan ({$startText} - {$endText})";
            $fileLabel = "MINGGUAN_" . $weekStart->format('Ymd') . "_" . $weekEnd->format('Ymd');
        } elseif ($tab === 'bulanan') {
            $tahun = intval($request->input('tahun_bulanan', Carbon::today()->year));
            $query->whereYear('tb_beli_bahan.tgl', $tahun);
            $periodeText = "Bulanan - Tahun " . $tahun;
            $fileLabel = "BULANAN_" . $tahun;
        } elseif ($tab === 'tahunan') {
            $tahunSekarang = intval(Carbon::today()->year);
            $tahunMulai = $tahunSekarang - 4;
            $query->whereYear('tb_beli_bahan.tgl', '>=', $tahunMulai)
                  ->whereYear('tb_beli_bahan.tgl', '<=', $tahunSekarang);
            $periodeText = "Tahunan ({$tahunMulai} - {$tahunSekarang})";
            $fileLabel = "TAHUNAN_{$tahunMulai}_{$tahunSekarang}";
        } else {
            // Harian
            $singleDate = $request->input('single_date', Carbon::today()->toDateString());
            $query->whereDate('tb_beli_bahan.tgl', $singleDate);
            $periodeText = "Harian - " . Carbon::parse($singleDate)->translatedFormat('l, d F Y');
            $fileLabel = "HARIAN_" . Carbon::parse($singleDate)->format('Ymd');
        }

        $items = $query->orderByDesc('tb_beli_bahan.tgl')->get();
        $cabang = $id_cabang ? DB::table('tb_cabang')->where('id_cabang', $id_cabang)->first() : null;

        return compact('items', 'periodeText', 'fileLabel', 'cabang', 'id_cabang', 'tab');
    }

    public function exportExcel(Request $request)
    {
        $filtered = $this->resolveFilter($request);
        $items = $filtered['items'];
        $periodeText = $filtered['periodeText'];
        $fileLabel = $filtered['fileLabel'];
        $cabang = $filtered['cabang'];

        $cabangSlug = $cabang ? '_' . str_replace(' ', '_', $cabang->nama_cabang) : '';
        $filename = "Laporan_Pengeluaran_{$fileLabel}{$cabangSlug}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($items, $periodeText, $cabang) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM

            // Header metadata
            fputcsv($handle, ['LAPORAN PENGELUARAN & PEMBELIAN BAHAN - SHANGHAEJO']);
            fputcsv($handle, ['Periode:', $periodeText]);
            fputcsv($handle, ['Cabang:', $cabang ? $cabang->nama_cabang : 'Semua Cabang']);
            fputcsv($handle, ['Waktu Export:', now()->translatedFormat('d F Y, H:i') . ' WIB']);
            fputcsv($handle, []);

            // Table Columns
            fputcsv($handle, ['No', 'Tanggal', 'Pengeluaran / Bahan', 'Qty', 'Harga Satuan (Rp)', 'Total (Rp)', 'Cabang', 'Supplier', 'Keterangan']);

            $totalPengeluaran = 0;
            $totalQty = 0;

            foreach ($items as $i => $row) {
                $totalPengeluaran += (float) $row->harga_total;
                $totalQty += (float) $row->qty;

                fputcsv($handle, [
                    $i + 1,
                    date('d/m/Y', strtotime($row->tgl)),
                    $row->nama_barang ?? $row->nama_bahan ?? '-',
                    $row->qty,
                    (int) $row->harga_satuan,
                    (int) $row->harga_total,
                    $row->nama_cabang ?? 'Pusat',
                    $row->supplier ?? '-',
                    $row->keterangan ?? '-'
                ]);
            }

            // Summary rows
            fputcsv($handle, []);
            fputcsv($handle, ['', '', 'TOTAL TRANSAKSI', count($items), '', '', '', '', '']);
            fputcsv($handle, ['', '', 'TOTAL QUANTITY', $totalQty, '', '', '', '', '']);
            fputcsv($handle, ['', '', 'TOTAL PENGELUARAN', (int) $totalPengeluaran, '', '', '', '', '']);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $filtered = $this->resolveFilter($request);
        $items = $filtered['items'];
        $periodeText = $filtered['periodeText'];
        $cabang = $filtered['cabang'];

        $totalPengeluaran = $items->sum('harga_total');
        $totalTransaksi = $items->count();
        $totalItem = $items->sum('qty');

        return view('admin.laporanpembelian.pdf', compact('items', 'periodeText', 'totalPengeluaran', 'totalTransaksi', 'totalItem', 'cabang'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file    = $request->file('file');
        $path    = $file->getRealPath();
        $handle  = fopen($path, 'r');

        $header        = fgetcsv($handle);
        $imported      = 0;
        $skipped       = 0;
        $rowLimit      = 1000; // Batas maksimal baris per import
        $defaultCabang = session('admin_cabang_filter') ?: 1;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Batasi jumlah baris agar tidak bisa DoS via file besar
                if ($imported + $skipped >= $rowLimit) {
                    fclose($handle);
                    DB::commit();
                    return redirect()->back()->with('success',
                        "Berhasil mengimpor {$imported} data (dibatasi {$rowLimit} baris pertama).");
                }

                if (count($row) < 3 || empty(trim($row[1] ?? ''))) {
                    $skipped++;
                    continue;
                }

                $tgl         = !empty($row[0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $row[0]))) : now()->toDateString();
                $namaBarang  = mb_substr(trim($row[1]), 0, 255); // Potong jika terlalu panjang
                $qty         = isset($row[2]) ? (float) preg_replace('/[^0-9.]/', '', $row[2]) : 1;
                $hargaSatuan = isset($row[3]) ? (float) preg_replace('/[^0-9.]/', '', $row[3]) : 0;
                $hargaTotal  = isset($row[4]) && !empty($row[4]) ? (float) preg_replace('/[^0-9.]/', '', $row[4]) : ($qty * $hargaSatuan);
                $supplier    = isset($row[5]) ? mb_substr(trim($row[5]), 0, 100) : null;
                $keterangan  = isset($row[6]) ? mb_substr(trim($row[6]), 0, 500) : null;

                DB::table('tb_beli_bahan')->insert([
                    'id_cabang'    => $defaultCabang,
                    'tgl'          => $tgl,
                    'nama_barang'  => $namaBarang,
                    'qty'          => $qty > 0 ? $qty : 1,
                    'harga_satuan' => $hargaSatuan,
                    'harga_total'  => $hargaTotal,
                    'supplier'     => $supplier,
                    'keterangan'   => $keterangan,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
                $imported++;
            }
            DB::commit();
            fclose($handle);

            return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data pengeluaran!");
        } catch (\Exception $e) {
            DB::rollBack();
            if (is_resource($handle)) fclose($handle);
            // Log detail error tapi jangan tampilkan ke user
            \Illuminate\Support\Facades\Log::error('Import CSV Gagal', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()->with('error', 'Gagal mengimpor file. Pastikan format CSV sudah benar.');
        }
    }

    private function getCabangBreakdown($query)
    {
        return (clone $query)
            ->leftJoin('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->select(DB::raw('COALESCE(tb_cabang.nama_cabang, "Tanpa Cabang") AS nama_cabang'), DB::raw('SUM(tb_beli_bahan.harga_total) AS total'))
            ->groupBy('tb_cabang.nama_cabang')
            ->orderByDesc('total')
            ->get();
    }

    // ✅ logika hitung total dipusatkan di sini
    private function hitungTotal($qty, $hargaSatuan): float
    {
        return $qty * $hargaSatuan;
    }

    private function cleanCurrency($value): int
    {
        if ($value === null || $value === '') return 0;
        $val = trim((string) $value);
        if (preg_match('/\.00$/', $val)) {
            $val = preg_replace('/\.00$/', '', $val);
        }
        return (int) str_replace(['.', ','], '', $val);
    }
}