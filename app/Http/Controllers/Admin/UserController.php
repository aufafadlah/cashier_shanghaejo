<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
public function index(Request $request) {
    $baseQuery = User::filterCabang();

    $totalUsers = (clone $baseQuery)->count();
    $aktif      = (clone $baseQuery)->where('is_active', 1)->count();
    $nonAktif   = (clone $baseQuery)->where('is_active', 0)->count();

    $query = User::with('cabang')->filterCabang()->orderBy('id_user', 'desc');

    if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%")
              ->orWhere('role', 'like', "%{$search}%");
        });
    }

    $users = $query->paginate(10)->withQueryString();

    return view('admin.user.index', compact('users', 'totalUsers', 'aktif', 'nonAktif'));
}

    public function create()
    {
        $cabangs = DB::table('tb_cabang')->where('is_active', 1)->orderBy('nama_cabang')->get();
        return view('admin.user.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cabang' => 'required',
            'name'      => 'required|string|max:100',
            'username'  => 'required|unique:tb_user,username',
            'password'  => 'required|min:6',
            'role'      => 'required|in:admin,kasir',
            'is_active' => 'required|in:0,1',
        ]);

        // Validasi cabang harus aktif
        $targetCabang = DB::table('tb_cabang')->where('id_cabang', $request->id_cabang)->first();
        if (!$targetCabang || !$targetCabang->is_active) {
            return redirect()->back()->withInput()->with('error', 'Cabang yang dipilih sedang dinonaktifkan.');
        }

        // Multi-Branch Validation:
        // Jika Admin sedang memfilter cabang tertentu di session, namun mencoba menginput/assign user ke cabang lain
        $adminCabangFilter = session('admin_cabang_filter');
        if ($adminCabangFilter && $adminCabangFilter != $request->id_cabang) {
            $namaCabang = $targetCabang ? $targetCabang->nama_cabang : 'cabang tersebut';
            return redirect()->back()->withInput()->with('error', "Anda harus mengganti cabang ke {$namaCabang} terlebih dahulu");
        }

        DB::table('tb_user')->insert([
            'id_cabang'  => $request->id_cabang,
            'name'       => $request->name,
            'username'   => $request->username,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'is_active'  => (int) $request->is_active,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = DB::table('tb_user')->where('id_user', $id)->first();

        if (!$user) {
            return redirect()->route('admin.user.index')->with('error', 'User tidak ditemukan!');
        }

        $cabangs = DB::table('tb_cabang')->where('is_active', 1)->orderBy('nama_cabang')->get();
        return view('admin.user.edit', compact('user', 'cabangs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_cabang' => 'required',
            'name'      => 'required|string|max:100',
            'username'  => 'required|unique:tb_user,username,' . $id . ',id_user',
            'role'      => 'required|in:admin,kasir',
            'is_active' => 'required|in:0,1',
        ]);

        // Validasi cabang harus aktif
        $targetCabang = DB::table('tb_cabang')->where('id_cabang', $request->id_cabang)->first();
        if (!$targetCabang || !$targetCabang->is_active) {
            return redirect()->back()->withInput()->with('error', 'Cabang yang dipilih sedang dinonaktifkan.');
        }

        // Multi-Branch Validation:
        // Jika Admin sedang memfilter cabang tertentu di session, namun mencoba update user ke cabang lain
        $adminCabangFilter = session('admin_cabang_filter');
        if ($adminCabangFilter && $adminCabangFilter != $request->id_cabang) {
            $namaCabang = $targetCabang ? $targetCabang->nama_cabang : 'cabang tersebut';
            return redirect()->back()->withInput()->with('error', "Anda harus mengganti cabang ke {$namaCabang} terlebih dahulu");
        }

        $data = [
            'id_cabang'  => $request->id_cabang,
            'name'       => $request->name,
            'username'   => $request->username,
            'role'       => $request->role,
            'is_active'  => (int) $request->is_active,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('tb_user')->where('id_user', $id)->update($data);

        // Jika user dinonaktifkan atau password diubah, hapus sesi user tersebut dari database
        if ((int) $request->is_active === 0 || $request->filled('password')) {
            DB::table('sessions')->where('user_id', $id)->delete();
        }

        return redirect()->route('admin.user.index')->with('success', 'Data user diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // ✅ tidak bisa hapus akun sendiri
        if ($id == Auth::id()) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Tidak dapat menghapus user: tidak bisa menghapus akun sendiri!');
        }

        // ✅ tidak bisa hapus user yang masih aktif (harus dinonaktifkan dulu)
        if ($user->is_active) {
            return redirect()->route('admin.user.index')
                ->with('error', 'tidak dapat menghapus user : masih berstatus aktif');
        }

        // ✅ Cek Penjualan (Kritis untuk audit trail)
        if (\App\Models\Penjualan::query()->where('id_user', $id)->exists()) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Tidak dapat menghapus user: masih ada data transaksi terhubung!');
        }

        // Hapus sesi user jika masih ada
        DB::table('sessions')->where('user_id', $id)->delete();

        // 3. Hapus
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User telah berhasil dihapus!');
    }
}