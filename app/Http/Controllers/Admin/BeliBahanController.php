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

    // 3. Ambil data
    $pengeluaran = $query->get();

    return view('admin.pembelian.index', compact('pengeluaran'));
}
    public function create()
    {
        $cabang = Cabang::all();
        return view('admin.pembelian.create', compact('cabang'));
    }

    public function store(Request $request)
    {
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
        $id_cabang = $user->id_cabang ?: $request->input('id_cabang');

        $daftarCabang = DB::table('tb_cabang')->orderBy('nama_cabang')->get();

        // ═══════════════════════════════════════════════════════════════
        // 1. HARIAN
        // ═══════════════════════════════════════════════════════════════
        $tanggal = $request->input('tanggal')
            ? Carbon::parse($request->input('tanggal'))->toDateString()
            : Carbon::today()->toDateString();

        $baseH = function () use ($id_cabang, $tanggal) {
            $q = DB::table('tb_beli_bahan')->whereDate('tgl', $tanggal);
            if ($id_cabang) $q->where('tb_beli_bahan.id_cabang', $id_cabang);
            return $q;
        };

        $baseCabangH = DB::table('tb_beli_bahan')
            ->join('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->whereDate('tb_beli_bahan.tgl', $tanggal);
        if ($id_cabang) $baseCabangH->where('tb_beli_bahan.id_cabang', $id_cabang);

        $harian = [
            'tanggal'          => $tanggal,
            'totalTransaksi'   => (clone $baseH())->count(),
            'totalPengeluaran' => (clone $baseH())->sum('harga_total') ?? 0,
            'totalItem'        => (clone $baseH())->sum('qty') ?? 0,
            'recentTransaksi'  => (clone $baseH())
                ->join('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_beli_bahan.tgl')
                ->orderByDesc('tb_beli_bahan.id_pengeluaran')
                ->limit(10)->get(),
            // Untuk harian, grafik data total pengeluaran per cabang
            'grafikData'       => (clone $baseCabangH)
                ->select('tb_cabang.nama_cabang', DB::raw('SUM(tb_beli_bahan.harga_total) AS total_pengeluaran'), DB::raw('COUNT(*) AS total_transaksi'))
                ->groupBy('tb_cabang.nama_cabang')
                ->get(),
            // Donut chart: pengeluaran per cabang
            'cabangBreakdown' => (clone $baseCabangH)
                ->select('tb_cabang.nama_cabang', DB::raw('SUM(tb_beli_bahan.harga_total) AS total'))
                ->groupBy('tb_cabang.nama_cabang')
                ->get(),
        ];

        // ═══════════════════════════════════════════════════════════════
        // 2. BULANAN
        // ═══════════════════════════════════════════════════════════════
        $bulanInput  = $request->input('bulan');
        $monthCarbon = $bulanInput
            ? Carbon::parse($bulanInput . '-01')->startOfMonth()
            : Carbon::today()->startOfMonth();
        $bulanStr = $monthCarbon->format('Y-m');

        $baseB = function () use ($id_cabang, $monthCarbon) {
            $q = DB::table('tb_beli_bahan')
                ->whereYear('tgl', $monthCarbon->year)
                ->whereMonth('tgl', $monthCarbon->month);
            if ($id_cabang) $q->where('tb_beli_bahan.id_cabang', $id_cabang);
            return $q;
        };

        $baseCabangB = DB::table('tb_beli_bahan')
            ->join('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->whereYear('tb_beli_bahan.tgl', $monthCarbon->year)
            ->whereMonth('tb_beli_bahan.tgl', $monthCarbon->month);
        if ($id_cabang) $baseCabangB->where('tb_beli_bahan.id_cabang', $id_cabang);

        $bulanan = [
            'bulanStr'         => $bulanStr,
            'monthCarbon'      => $monthCarbon,
            'totalTransaksi'   => (clone $baseB())->count(),
            'totalPengeluaran' => (clone $baseB())->sum('harga_total') ?? 0,
            'totalItem'        => (clone $baseB())->sum('qty') ?? 0,
            'recentTransaksi'  => (clone $baseB())
                ->join('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_beli_bahan.tgl')
                ->orderByDesc('tb_beli_bahan.id_pengeluaran')
                ->limit(10)->get(),
            'grafikData'       => (clone $baseB())
                ->select(DB::raw('DAY(tgl) AS hari'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(harga_total) AS total_pengeluaran'))
                ->groupBy(DB::raw('DAY(tgl)'))->orderBy(DB::raw('DAY(tgl)'))->get(),
            'cabangBreakdown' => (clone $baseCabangB)
                ->select('tb_cabang.nama_cabang', DB::raw('SUM(tb_beli_bahan.harga_total) AS total'))
                ->groupBy('tb_cabang.nama_cabang')
                ->get(),
        ];

        // ═══════════════════════════════════════════════════════════════
        // 3. TAHUNAN
        // ═══════════════════════════════════════════════════════════════
        $tahun = intval($request->input('tahun', Carbon::today()->year));

        $baseT = function () use ($id_cabang, $tahun) {
            $q = DB::table('tb_beli_bahan')->whereYear('tgl', $tahun);
            if ($id_cabang) $q->where('tb_beli_bahan.id_cabang', $id_cabang);
            return $q;
        };

        $baseCabangT = DB::table('tb_beli_bahan')
            ->join('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
            ->whereYear('tb_beli_bahan.tgl', $tahun);
        if ($id_cabang) $baseCabangT->where('tb_beli_bahan.id_cabang', $id_cabang);

        $tahunan = [
            'tahun'            => $tahun,
            'totalTransaksi'   => (clone $baseT())->count(),
            'totalPengeluaran' => (clone $baseT())->sum('harga_total') ?? 0,
            'totalItem'        => (clone $baseT())->sum('qty') ?? 0,
            'recentTransaksi'  => (clone $baseT())
                ->join('tb_cabang', 'tb_beli_bahan.id_cabang', '=', 'tb_cabang.id_cabang')
                ->select('tb_beli_bahan.*', 'tb_cabang.nama_cabang')
                ->orderByDesc('tb_beli_bahan.tgl')
                ->orderByDesc('tb_beli_bahan.id_pengeluaran')
                ->limit(10)->get(),
            'grafikData'       => (clone $baseT())
                ->select(DB::raw('MONTH(tgl) AS bulan'), DB::raw('COUNT(*) AS total_transaksi'), DB::raw('SUM(harga_total) AS total_pengeluaran'))
                ->groupBy(DB::raw('MONTH(tgl)'))->orderBy(DB::raw('MONTH(tgl)'))->get(),
            'cabangBreakdown' => (clone $baseCabangT)
                ->select('tb_cabang.nama_cabang', DB::raw('SUM(tb_beli_bahan.harga_total) AS total'))
                ->groupBy('tb_cabang.nama_cabang')
                ->get(),
        ];

        return view('admin.laporanpembelian.index', compact(
            'isAdmin', 'id_cabang', 'daftarCabang',
            'harian', 'bulanan', 'tahunan'
        ));
    }

    // ✅ logika hitung total dipusatkan di sini
    private function hitungTotal($qty, $hargaSatuan): float
    {
        return $qty * $hargaSatuan;
    }
}