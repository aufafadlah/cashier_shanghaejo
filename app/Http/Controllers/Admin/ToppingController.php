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

// Tambahkan 'admin.' di depannya
    return view('admin.toping.index', compact('topings', 'totalToping', 'aktif', 'nonAktif'));    
    }

 
public function create()
{
    // Tambahkan 'admin.' di depannya agar merujuk ke folder yang benar
    return view('admin.toping.create');
}    

 public function store(Request $request)
{
    $request->validate([
        'nama_toping'  => 'required',
        'harga_toping' => 'required|numeric',
        'is_active'    => 'required'
    ]);

    // Hapus baris created_at dan updated_at karena kolomnya tidak ada di tabel tb_toping
    DB::table('tb_toping')->insert([
        'nama_toping'  => $request->nama_toping,
        'harga_toping' => $request->harga_toping,
        'is_active'    => $request->is_active,
    ]);

    return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil ditambah!');
}

public function edit($id)
{
    // Pastikan nama variabel di sini $toping (satu 'p')
    $toping = DB::table('tb_toping')->where('id_toping', $id)->first();
    
    // Maka di sini compact-nya juga harus 'toping'
    return view('admin.toping.edit', compact('toping'));
}
      public function update(Request $request, $id)
{
    // 1. Validasi input
    $request->validate([
        'nama_toping'  => 'required',
        'harga_toping' => 'required|numeric',
        'is_active'    => 'required',
    ]);

    // 2. Proses Update ke Database
    DB::table('tb_toping')->where('id_toping', $id)->update([
        'nama_toping'  => $request->nama_toping, // Pastikan pakai nama_toping, bukan nama_menu
        'harga_toping' => $request->harga_toping,
        'is_active'    => $request->is_active,
        // JANGAN masukkan 'updated_at' di sini karena kolomnya tidak ada di tabel
    ]);

    // 3. Redirect kembali
    return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil diupdate!');
}
    public function destroy($id)
    {
        DB::table('tb_toping')->where('id_toping', $id)->delete();
        return redirect()->route('admin.toping.index')->with('success', 'Topping berhasil dihapus!');
    }
}