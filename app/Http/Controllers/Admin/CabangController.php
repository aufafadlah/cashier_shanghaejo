<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
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

    public function create()
    {
        return view('admin.cabang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cabang' => 'required',
            'alamat'      => 'required',
            'is_active'   => 'nullable|boolean',
        ]);

        DB::table('tb_cabang')->insert([
            'nama_cabang' => $request->nama_cabang,
            'alamat'      => $request->alamat,
            'is_active'   => $request->boolean('is_active'),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()
            ->route('admin.cabang.index')
            ->with('success', 'Cabang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $cabang = DB::table('tb_cabang')->where('id_cabang', $id)->first();

        // ✅ null check
        if (!$cabang) {
            return redirect()->route('admin.cabang.index')->with('error', 'Cabang tidak ditemukan!');
        }

        return view('admin.cabang.edit', compact('cabang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_cabang' => 'required',
            'alamat'      => 'required',
        ]);

        DB::table('tb_cabang')->where('id_cabang', $id)->update([
            'nama_cabang' => $request->nama_cabang,
            'alamat'      => $request->alamat,
            'is_active'   => $request->boolean('is_active'), // ✅ cast boolean
            'updated_at'  => now(),
        ]);

        return redirect()->route('admin.cabang.index')->with('success', 'Data cabang diperbarui!');
    }

    public function destroy($id)
    {
        // ✅ cek apakah cabang masih punya user
        $adaUser = DB::table('tb_user')->where('id_cabang', $id)->exists();
        if ($adaUser) {
            return redirect()->route('admin.cabang.index')
                ->with('error', 'Cabang tidak bisa dihapus karena masih memiliki user aktif!');
        }

        // ✅ cek apakah cabang masih punya transaksi
        $adaTransaksi = DB::table('tb_penjualan')->where('id_cabang', $id)->exists();
        if ($adaTransaksi) {
            return redirect()->route('admin.cabang.index')
                ->with('error', 'Cabang tidak bisa dihapus karena masih memiliki data transaksi!');
        }

        DB::table('tb_cabang')->where('id_cabang', $id)->delete();
        return redirect()->route('admin.cabang.index')->with('success', 'Cabang telah dihapus!');
    }

    public function show($id)
    {
        // ✅ konsisten pakai DB::table, bukan Eloquent
        $cabang = DB::table('tb_cabang')->where('id_cabang', $id)->first();

        if (!$cabang) {
            return redirect()->route('admin.cabang.index')->with('error', 'Cabang tidak ditemukan!');
        }

        return view('admin.cabang.show', compact('cabang'));
    }
}