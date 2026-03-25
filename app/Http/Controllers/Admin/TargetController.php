<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Cabang; // Pastikan ini di-import untuk dropdown cabang
use Illuminate\Http\Request;

class TargetController extends Controller
{
    // TAMBAHKAN FUNGSI INI
    
public function index()
{
    $cabangs = Cabang::all();

    // Ambil data target hari ini dengan JOIN manual ke tabel tb_cabang
    $targets = DB ::table('tb_target')
        ->join('tb_cabang', 'tb_target.id_cabang', '=', 'tb_cabang.id_cabang')
        ->select('tb_target.*', 'tb_cabang.nama_cabang')
        ->whereDate('tb_target.tanggal', today())
        ->get();

    return view('admin.target.index', compact('cabangs', 'targets'));
}
public function store(Request $request)
{
    $request->validate([
        'stok_target' => 'required|array',
    ]);

    $tanggalHariIni = now()->format('Y-m-d');

    foreach ($request->stok_target as $id_cabang => $stok) {
        // Jika input kosong atau null, otomatis set 100
        $nilaiStok = ($stok === null || $stok === '') ? 100 : $stok;

        \App\Models\Target::updateOrCreate(
            [
                'id_cabang' => $id_cabang,
                'tanggal'   => $tanggalHariIni,
                'id_menu'   => 1
            ],
            [
                'stok_target' => $nilaiStok,
                'over_target' => 0
            ]
        );
    }

    // return redirect()->route('admin.target.index')->with('success', 'Data Berhasil Disimpan!');
    return response()->json([
        'success' => true,
        'message' => 'Data Berhasil Disimpan!'
    ]);
}
}