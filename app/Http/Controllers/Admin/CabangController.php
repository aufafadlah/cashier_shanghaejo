<?php

namespace App\Http\Controllers\Admin;
use App\Models\Cabang;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
public function index()
{
    // Menggunakan Eloquent: Cabang::all() otomatis memanggil semua data
   $cabangs = Cabang::all();
  

    // Eloquent collection memiliki method count() dan where() yang sangat powerful
    $totalCabang = $cabangs->count();
    $aktif       = $cabangs->where('is_active', true)->count();
    $nonAktif    = $cabangs->where('is_active', false)->count();

    return view('admin.cabang.index', compact('cabangs', 'totalCabang', 'aktif', 'nonAktif'));
}

    public function create()
    {
        return view('admin.cabang.create');
    }

public function store(Request $request)
{
    // 1. Validasi
    $validated = $request->validate([
        'nama_cabang' => 'required',
        'alamat'      => 'required',
        'is_active'   => 'nullable|boolean',
    ]);

    // 2. Simpan menggunakan Eloquent (Otomatis handle created_at/updated_at)
    Cabang::create($validated);

    return redirect()->route('admin.cabang.index')->with('success', 'Cabang berhasil ditambahkan');
}

public function edit($id)
{
    // Menggunakan Eloquent agar konsisten
    $cabang = Cabang::findOrFail($id); 

    return view('admin.cabang.edit', compact('cabang'));
}

public function update(Request $request, $id)
{
    $cabang = Cabang::findOrFail($id);

    $validated = $request->validate([
        'nama_cabang' => 'required|string|max:255',
        'alamat'      => 'required|string',
        'is_active'   => 'nullable|boolean', // Pastikan ini ditangani
    ]);

    // Menggunakan $validated lebih aman daripada $request->all()
    $cabang->update($validated);

    return redirect()->route('admin.cabang.index')->with('success', 'Data cabang diperbarui!');
}
public function destroy($id)
{
    // 1. Ambil data cabang
    $cabang = Cabang::findOrFail($id);

    // 2. Cek relasi menggunakan method model
    if ($cabang->menus()->exists()) {
        return redirect()->route('admin.cabang.index')->with('error', 'Masih ada menu terhubung!');
    }

    // 3. Cek User - Gunakan Query Builder secara eksplisit jika Intelephense bingung
    if (\App\Models\User::query()->where('id_cabang', $id)->exists()) {
        return redirect()->route('admin.cabang.index')->with('error', 'Masih ada user aktif!');
    }

    // 4. Cek Penjualan
    if (\App\Models\Penjualan::query()->where('id_cabang', $id)->exists()) {
        return redirect()->route('admin.cabang.index')->with('error', 'Masih ada data transaksi!');
    }

    // 5. Hapus
    $cabang->delete();

    return redirect()->route('admin.cabang.index')->with('success', 'Cabang telah dihapus!');
}
public function show($id)
{
    // findOrFail otomatis mengarahkan ke 404 jika data tidak ditemukan
    $cabang = Cabang::findOrFail($id);

    return view('admin.cabang.show', compact('cabang'));
}
}