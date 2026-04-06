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
    // Validasi input: stok_target harus array
    $request->validate([
        'stok_target' => 'required|array',
    ]);

    $tanggalHariIni = now()->format('Y-m-d');

    // Struktur input dari view sebaiknya: name="stok_target[id_cabang][id_menu]"
    foreach ($request->stok_target as $id_cabang => $menus) {
        foreach ($menus as $id_menu => $stok) {
            
            // Logika: Jika input kosong/null, otomatis set 100
            $nilaiStok = ($stok === null || $stok === '') ? 100 : $stok;

            \App\Models\Target::updateOrCreate(
                [
                    'id_cabang' => $id_cabang,
                    'id_menu'   => $id_menu, // Menggunakan id_menu dinamis
                    'tanggal'   => $tanggalHariIni,
                ],
                [
                    'stok_target' => $nilaiStok,
                    'over_target' => 0, // Reset over target saat admin set ulang stok
                    'updated_at'  => now()
                ]
            );
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Data Stok Berhasil Disimpan!'
    ]);
}
}