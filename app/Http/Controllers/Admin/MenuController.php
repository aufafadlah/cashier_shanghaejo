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
        // 1. Ambil data menu beserta SEMUA relasi cabangnya (dengan filter search jika ada)
        $query = Menu::with('cabangs')->orderBy('id_menu', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama_menu', 'like', "%{$search}%");
        }

        $menus = $query->paginate(10)->withQueryString();

        // 2. Ambil semua cabang untuk referensi
        $branches = Cabang::all();
        $allCabangIds = $branches->pluck('id_cabang');

        // 3. Hitung statistik
        $totalMenu = DB::table('tb_menu')->count();
        // Hitung menu yg setidaknya aktif di 1 cabang
        $aktif = DB::table('tb_menu')
            ->whereExists(function($q) {
                $q->select(DB::raw(1))
                  ->from('branch_menu')
                  ->whereColumn('branch_menu.menu_id', 'tb_menu.id_menu')
                  ->where('branch_menu.is_active', true);
            })->count();
        $nonAktif = $totalMenu - $aktif;

        // 4. Kirim ke view
        return view('admin.menu.index', compact('menus', 'branches', 'totalMenu', 'aktif', 'nonAktif'));
    }

    /**
     * Menampilkan form untuk tambah menu baru
     */
    public function create()
    {
        // Ambil SEMUA data cabang agar semua cabang bisa dipilih saat membuat menu
        $branches = Cabang::orderBy('nama_cabang')->get();
        return view('admin.menu.create', compact('branches'));
    }

    /**
     * Menyimpan data menu baru ke database (Many to Many)
     */
    public function store(Request $request)
    {
        if ($request->has('harga_cabang') && is_array($request->harga_cabang)) {
            $cleaned = [];
            foreach ($request->harga_cabang as $k => $v) {
                $cleaned[$k] = $this->cleanCurrency($v);
            }
            $request->merge(['harga_cabang' => $cleaned]);
        }

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

        // Ambil SEMUA cabang agar form edit menampilkan semua pilihan
        $branches = Cabang::orderBy('nama_cabang')->get();

        return view('admin.menu.edit', compact('menu', 'branches'));
    }

    /**
     * Memperbarui data menu lama beserta relasi harganya di database
     */
    public function update(Request $request, $id)
    {
        if ($request->has('harga_cabang') && is_array($request->harga_cabang)) {
            $cleaned = [];
            foreach ($request->harga_cabang as $k => $v) {
                $cleaned[$k] = $this->cleanCurrency($v);
            }
            $request->merge(['harga_cabang' => $cleaned]);
        }

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

        // Per-branch handling:
        // - Jika checkbox dicentang -> insert/attach atau updateExistingPivot (is_active = true)
        // - Jika checkbox tidak dicentang tapi pivot sudah ada -> update is_active = false (jangan detach)
        $allBranchIds = DB::table('tb_cabang')->pluck('id_cabang')->toArray();

        foreach ($allBranchIds as $branchId) {
            $isSelected = in_array($branchId, $request->branch_ids ?? []);
            $hargaUntukCabang = $request->harga_cabang[$branchId] ?? 0;

            $exists = DB::table('branch_menu')
                        ->where('menu_id', $menu->id_menu)
                        ->where('branch_id', $branchId)
                        ->exists();

            if ($isSelected) {
                if ($exists) {
                    DB::table('branch_menu')
                        ->where('menu_id', $menu->id_menu)
                        ->where('branch_id', $branchId)
                        ->update([
                            'harga_menu' => $hargaUntukCabang,
                            'is_active'  => true,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('branch_menu')->insert([
                        'menu_id'    => $menu->id_menu,
                        'branch_id'  => $branchId,
                        'harga_menu' => $hargaUntukCabang,
                        'is_active'  => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Jika tidak dipilih dan pivot ada, jangan hapus — set inactive
                if ($exists) {
                    DB::table('branch_menu')
                        ->where('menu_id', $menu->id_menu)
                        ->where('branch_id', $branchId)
                        ->update([
                            'is_active'  => false,
                            'updated_at' => now(),
                        ]);
                }
            }
        }

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

    private function cleanCurrency($value): int
    {
        if ($value === null || $value === '') return 0;
        $val = trim((string) $value);
        if (preg_match('/\.00$/', $val)) {
            $val = preg_replace('/\.00$/', '', $val);
        }
        return (int) str_replace(['.', ','], '', $val);
    }
}