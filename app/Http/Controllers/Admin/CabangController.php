<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib untuk query database

class CabangController extends Controller
{
    /**
     * Tampilkan Halaman List Cabang (Daftar Tabel)
     * URL: admin/cabang
     */
  public function index()
{
    $cabangs = DB::table('tb_cabang')->get();

    $totalCabang = $cabangs->count();
    $aktif = $cabangs->where('is_active', 1)->count();
    $nonAktif = $cabangs->where('is_active', 0)->count();

    return view('admin.cabang.index', compact(
        'cabangs',
        'totalCabang',
        'aktif',
        'nonAktif'
    ));
}

    /**
     * Tampilkan Form Tambah Cabang (Sesuai Desain Figma)
     * URL: admin/cabang/create
     */
    public function create()
    {
        return view('admin.cabang.create');
    }

    /**
     * Proses Simpan Cabang Baru ke Database
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_cabang' => 'required',
            'alamat'      => 'required',
        ]);

        // 2. Simpan Data ke tb_cabang
        DB::table('tb_cabang')->insert([
            'nama_cabang' => $request->nama_cabang,
            'alamat'      => $request->alamat,
            'is_active'   => 1, // Default aktif
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 3. Kembali ke halaman tabel dengan pesan sukses
        return redirect()->route('cabang.index')->with('success', 'Cabang baru berhasil didaftarkan!');
    }

    /**
     * Tampilkan Form Edit Cabang
     */
    public function edit($id)
    {
        // Cari data cabang berdasarkan id_cabang
        $cabang = DB::table('tb_cabang')->where('id_cabang', $id)->first();

        return view('admin.cabang.edit', compact('cabang'));
    }

    /**
     * Proses Update Data Cabang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_cabang' => 'required',
            'alamat'      => 'required',
        ]);

        DB::table('tb_cabang')->where('id_cabang', $id)->update([
            'nama_cabang' => $request->nama_cabang,
            'alamat'      => $request->alamat,
            'is_active'   => $request->is_active,
            'updated_at'  => now(),
        ]);

        return redirect()->route('cabang.index')->with('success', 'Data cabang diperbarui!');
    }

    /**
     * Hapus Cabang
     */
    public function destroy($id)
    {
        DB::table('tb_cabang')->where('id_cabang', $id)->delete();
        return redirect()->route('cabang.index')->with('success', 'Cabang telah dihapus!');
    }
}