<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeliBahan;
use App\Models\Cabang;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        $totalCabang = DB::table('tb_cabang')->count();
        $aktif       = DB::table('tb_cabang')->where('is_active', 1)->count();
        $nonAktif    = DB::table('tb_cabang')->where('is_active', 0)->count();

        $query = Cabang::query()->orderBy('id_cabang', 'desc');

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
            'nama_cabang' => 'required|string|max:255',
            'alamat'      => 'required|string',
            'is_active'   => 'nullable|in:0,1',
        ]);

        // 2. Default is_active = true jika tidak dipilih
        $validated['is_active'] = $request->filled('is_active') ? (bool) $request->is_active : true;

        // 3. Simpan menggunakan Eloquent
        try {
            Cabang::query()->create($validated);
            return redirect()->route('admin.cabang.index')->with('success', 'Cabang berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $cabang = Cabang::query()->findOrFail($id); 

        return view('admin.cabang.edit', compact('cabang'));
    }

    public function update(Request $request, $id)
    {
        $cabang = Cabang::query()->findOrFail($id);

        $validated = $request->validate([
            'nama_cabang' => 'required|string|max:255',
            'alamat'      => 'required|string',
            'is_active'   => 'required|in:0,1',
        ]);

        $oldStatus = (bool) $cabang->is_active;
        $newStatus = (bool) $validated['is_active'];

        $cabang->update($validated);

        if ($oldStatus !== $newStatus) {
            if (!$newStatus) {
                // 1. Cabang dinonaktifkan:
                // Nonaktifkan menu di cabang ini (branch_menu.is_active = 0)
                DB::table('branch_menu')
                    ->where('branch_id', $cabang->id_cabang)
                    ->update(['is_active' => false, 'updated_at' => now()]);

                // Nonaktifkan user di cabang ini (kecuali akun admin yang sedang login)
                DB::table('tb_user')
                    ->where('id_cabang', $cabang->id_cabang)
                    ->where('id_user', '!=', Auth::id())
                    ->update(['is_active' => false, 'updated_at' => now()]);

                // Hapus sesi pengguna di cabang ini agar langsung logout
                $affectedUserIds = DB::table('tb_user')
                    ->where('id_cabang', $cabang->id_cabang)
                    ->where('id_user', '!=', Auth::id())
                    ->pluck('id_user');

                if ($affectedUserIds->isNotEmpty() && Schema::hasTable('sessions')) {
                    DB::table('sessions')->whereIn('user_id', $affectedUserIds)->delete();
                }

                // Reset filter admin jika cabang yang difilter dinonaktifkan
                if (session('admin_cabang_filter') == $cabang->id_cabang) {
                    session()->forget('admin_cabang_filter');
                }
            } else {
                // 2. Cabang diaktifkan kembali:
                // Aktifkan kembali menu yang terdaftar di cabang ini
                DB::table('branch_menu')
                    ->where('branch_id', $cabang->id_cabang)
                    ->update(['is_active' => true, 'updated_at' => now()]);

                // Aktifkan kembali user di cabang ini
                DB::table('tb_user')
                    ->where('id_cabang', $cabang->id_cabang)
                    ->update(['is_active' => true, 'updated_at' => now()]);
            }
        }

        return redirect()->route('admin.cabang.index')->with('success', 'Data cabang dan status terkait berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // 1. Ambil data cabang
        $cabang = Cabang::query()->findOrFail($id);

        // 2. Cegah admin menghapus cabang tempat ia sendiri terdaftar
        if (Auth::user()->id_cabang == $id) {
            return redirect()->route('admin.cabang.index')->with('error', 'Tidak dapat menghapus cabang: Anda sedang terdaftar pada cabang ini!');
        }

        // 3. Cek Penjualan (Kritis untuk audit trail)
        if (Penjualan::query()->where('id_cabang', $id)->exists()) {
            return redirect()->route('admin.cabang.index')->with('error', 'Tidak dapat menghapus cabang: masih ada data transaksi penjualan terhubung!');
        }

        // 4. Cek Pembelian Bahan (Kritis untuk audit trail keuangan)
        if (BeliBahan::query()->where('id_cabang', $id)->exists()) {
            return redirect()->route('admin.cabang.index')->with('error', 'Tidak dapat menghapus cabang: masih ada data pembelian bahan terhubung!');
        }

        // 5. Bersihkan sesi user di cabang ini jika ada tabel sessions
        $userIds = User::query()->where('id_cabang', $id)->pluck('id_user');
        if ($userIds->isNotEmpty() && Schema::hasTable('sessions')) {
            DB::table('sessions')->whereIn('user_id', $userIds)->delete();
        }

        // 6. Hapus User yang terhubung ke cabang ini
        User::query()->where('id_cabang', $id)->delete();

        // 7. Hapus relasi menu di pivot table (branch_menu)
        $cabang->menus()->detach();

        // 8. Hapus data cabang (menggunakan query builder eksplisit agar terbebas dari error Intelephense di VS Code)
        Cabang::query()->where('id_cabang', $id)->delete();

        return redirect()->route('admin.cabang.index')->with('success', 'Cabang telah berhasil dihapus!');
    }

    public function show($id)
    {
        $cabang = Cabang::query()->findOrFail($id);

        return view('admin.cabang.show', compact('cabang'));
    }
}