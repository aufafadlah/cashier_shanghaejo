<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ToppingController extends Controller
{
    public function index()
    {
        $topings = DB::table('tb_toping')->get();
        $totalToping = $topings->count();
        $aktif = $topings->where('is_active', 1)->count();
        $nonAktif = $topings->where('is_active', 0)->count();

        return view('admin.toping.index', compact('topings', 'totalToping', 'aktif', 'nonAktif'));
    }

    public function create()
    {
        return view('admin.toping.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toping'  => 'required',
            'harga_toping' => 'required|numeric|min:0', // ✅ tambah min:0
            'is_active'    => 'required',
        ]);

        DB::table('tb_toping')->insert([
            'nama_toping'  => $request->nama_toping,
            'harga_toping' => $request->harga_toping,
            'is_active'    => (int) $request->is_active, // ✅ cast ke integer
        ]);

        return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil ditambah!');
    }

    public function edit($id)
    {
        $toping = DB::table('tb_toping')->where('id_toping', $id)->first();

        // ✅ null check
        if (!$toping) {
            return redirect()->route('admin.toping.index')->with('error', 'Topping tidak ditemukan!');
        }

        return view('admin.toping.edit', compact('toping'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_toping'  => 'required',
            'harga_toping' => 'required|numeric|min:0', // ✅ tambah min:0
            'is_active'    => 'required',
        ]);

        DB::table('tb_toping')->where('id_toping', $id)->update([
            'nama_toping'  => $request->nama_toping,
            'harga_toping' => $request->harga_toping,
            'is_active'    => (int) $request->is_active, // ✅ cast ke integer
        ]);

        return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil diupdate!');
    }

public function destroy($id)
{
    // cek apakah topping pernah dipakai di transaksi
    $dipakai = DB::table('tb_detail_penjualan_toping')->where('id_toping', $id)->exists();

    if ($dipakai) {
        return redirect()->route('admin.toping.index')->with('error', 'Topping tidak bisa dihapus karena masih digunakan dalam transaksi!');
    }

    DB::table('tb_toping')->where('id_toping', $id)->delete();
    return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil dihapus!');
}
}