<?php

namespace App\Http\Controllers\Admin;
use App\Models\Cabang;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CabangController extends Controller
{
public function index(Request $request)
{
    $totalCabang = Cabang::count();
    $aktif       = Cabang::where('is_active', true)->count();
    $nonAktif    = Cabang::where('is_active', false)->count();

    $query = Cabang::orderBy('id_cabang', 'desc');

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('nama_cabang', 'like', "%{$search}%")
              ->orWhere('alamat', 'like', "%{$search}%");
        });
    }

    $cabangs = $query->paginate(10)->withQueryString();

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

    // 2. Cek Penjualan (Kritis untuk audit trail)
    if (\App\Models\Penjualan::query()->where('id_cabang', $id)->exists()) {
        return redirect()->route('admin.cabang.index')->with('error', 'Tidak dapat menghapus cabang: masih ada data transaksi terhubung!');
    }

    // 3. Jika aman (tidak ada transaksi), hapus User yang terhubung ke cabang ini
    \App\Models\User::query()->where('id_cabang', $id)->delete();

    // 4. Hapus relasi menu di pivot table (branch_menu)
    $cabang->menus()->detach();

    // 5. Hapus
    $cabang->delete();

    return redirect()->route('admin.cabang.index')->with('success', 'Cabang telah berhasil dihapus!');
}
public function show($id)
{
    // findOrFail otomatis mengarahkan ke 404 jika data tidak ditemukan
    $cabang = Cabang::findOrFail($id);

    return view('admin.cabang.show', compact('cabang'));
}
}