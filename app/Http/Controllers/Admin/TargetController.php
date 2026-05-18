<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Target;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TargetController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();

        $targets = DB::table('tb_target')
            ->join('tb_cabang', 'tb_target.id_cabang', '=', 'tb_cabang.id_cabang')
            ->join('tb_menu', 'tb_target.id_menu', '=', 'tb_menu.id_menu')
            ->select('tb_target.*', 'tb_cabang.nama_cabang', 'tb_menu.nama_menu')
            ->whereDate('tb_target.tanggal', today())
            ->get();

        return view('admin.target.index', compact('cabangs', 'targets'));
    }

    public function store(Request $request)
    {
        // ✅ validasi lengkap termasuk tipe data tiap value
        $request->validate([
            'stok_target'       => 'required|array',
            'stok_target.*.*'   => 'nullable|integer|min:0',
        ]);

        $tanggalHariIni = now()->format('Y-m-d');

        foreach ($request->stok_target as $id_cabang => $menus) {
            // ✅ validasi id_cabang ada di database
            $cabangAda = DB::table('tb_cabang')->where('id_cabang', $id_cabang)->exists();
            if (!$cabangAda) continue;

            foreach ($menus as $id_menu => $stok) {
                // ✅ validasi id_menu ada di database
                $menuAda = DB::table('tb_menu')->where('id_menu', $id_menu)->exists();
                if (!$menuAda) continue;

                // ✅ default 100 jika kosong, dokumentasi jelas di kode
                $nilaiStok = ($stok === null || $stok === '') ? 100 : (int) $stok;

                Target::updateOrCreate(
                    [
                        'id_cabang' => $id_cabang,
                        'id_menu'   => $id_menu,
                        'tanggal'   => $tanggalHariIni,
                    ],
                    [
                        'stok_target' => $nilaiStok,
                        'over_target' => 0,
                        'updated_at'  => now(),
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data stok berhasil disimpan! (default 100 untuk input kosong)',
        ]);
    }
}