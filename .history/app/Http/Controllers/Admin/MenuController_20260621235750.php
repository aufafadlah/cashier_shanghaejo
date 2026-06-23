<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Menampilkan halaman utama daftar menu beserta statistik card
     */
    
public function index(Request $request)
{
    // 1. Ambil data menu dengan relasi cabang
    $query = Menu::with('cabangs');

    // Filter berdasarkan cabang jika user memilih di dropdown navbar
    if ($request->has('id_cabang') && !empty($request->id_cabang)) {
        $query->whereHas('cabangs', function($q) use ($request) {
            $q->where('tb_cabang.id_cabang', $request->id_cabang);
        });
    }

    $menus = $query->get();

    // 2. Ambil data cabang murni tanpa duplikat nama
    $branches = Cabang::all()->unique('nama_cabang');

    // 3. Hitung statistik untuk x-card
    $totalMenu = DB::table('tb_menu')->count();
    $aktif = DB::table('branch_menu')->where('is_active', true)->count();
    $nonAktif = DB::table('branch_menu')->where('is_active', false)->count();

    // 4. Kirim variabel ke view
    return view('admin.menu.index', compact('menus', 'branches', 'totalMenu', 'aktif', 'nonAktif'));
}
    /**
     * Menampilkan form untuk tambah menu baru
     */
    public function create()
    {
        // Ambil data cabang murni dan saring agar nama ganda hilang dari form checkbox
        $branches = Cabang::all()->unique('nama_cabang');
        return view('admin.menu.create', compact('branches'));
    }

    /**
     * Menyimpan data menu baru ke database (Many to Many)
     */
    public function store(Request $request)
    {
        // Validasi bersih dari kategori_menu
        $request->validate([
            'nama_menu'    => 'required|string|max:100',
            'branch_ids'   => 'required|array',
            'harga_cabang' => 'required|array',
        ]);

        // Simpan data induk ke tb_menu
        $menu = Menu::create([
            'nama_menu'   => $request->nama_menu,
            'harga_menu'  => 0,         
            'status_menu' => 'aktif',    
            'is_active'   => 1,          
        ]);

        // Ambil harga masing-masing cabang dari form dinamis
        $syncData = [];
        foreach ($request->branch_ids as $branchId) {
            $syncData[$branchId] = [
                'harga_menu' => $request->harga_cabang[$branchId] ?? 0,
                'is_active'  => true
            ];
        }

        // Hubungkan menu baru dengan cabang-cabangnya di tabel jembatan (branch_menu)
        $menu->cabangs()->attach($syncData);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil disimpan!');
    }

    /**
     * Menampilkan form edit untuk data menu lama
     */
    public function edit($id)
    {
        // Ambil data menu dengan primary key custom id_menu beserta relasinya
        $menu = Menu::with('cabangs')->where('id_menu', $id)->first();

        if (!$menu) {
            return redirect()->route('admin.menu.index')->with('error', 'Data menu tidak ditemukan!');
        }

        // Saring nama cabang ganda biar tidak merusak form edit checkbox
        $branches = Cabang::all()->unique('nama_cabang'); 

        return view('admin.menu.edit', compact('menu', 'branches'));
    }

    /**
     * Memperbarui data menu lama beserta relasi harganya di database
     */
    public function update(Request $request, $id)
    {
        // Validasi bersih dari kategori_menu
        $request->validate([
            'nama_menu'    => 'required|string|max:100',
            'branch_ids'   => 'required|array',
            'harga_cabang' => 'required|array',
        ]);

        // Ambil query builder eksplisit agar Intelephense tidak merah
        $menu = Menu::query()->where('id_menu', $id)->first();
        
        if (!$menu) {
            return redirect()->route('admin.menu.index')->with('error', 'Data tidak ditemukan!');
        }

        // Update data induk tb_menu
        $menu->update([
            'nama_menu'   => $request->nama_menu,
            'harga_menu'  => 0,
            'status_menu' => 'aktif',
            'is_active'   => 1,
        ]);

        // Sinkronisasikan ulang harga per cabang ke tabel branch_menu
        $syncData = [];
        foreach ($request->branch_ids as $branchId) {
            $syncData[$branchId] = [
                'harga_menu' => $request->harga_cabang[$branchId] ?? 0,
                'is_active'  => true
            ];
        }
        
        $menu->cabangs()->sync($syncData);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diupdate!');
    }

    /**
     * Menghapus data menu dari database
     */
    public function destroy($id)
    {
        // Pakai query builder eksplisit untuk menjinakkan error 'Not enough arguments' di VS Code
        $deleted = Menu::query()->where('id_menu', $id)->delete();
        
        if ($deleted) {
            return redirect()->route('admin.menu.index')->with('success', 'Menu telah dihapus!');
        }
        
        return redirect()->route('admin.menu.index')->with('error', 'Menu gagal dihapus!');
    }
}