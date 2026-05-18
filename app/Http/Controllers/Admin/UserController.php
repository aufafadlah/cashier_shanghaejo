<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = DB::table('tb_user')
            ->leftJoin('tb_cabang', 'tb_user.id_cabang', '=', 'tb_cabang.id_cabang')
            ->select('tb_user.*', 'tb_cabang.nama_cabang')
            ->get();

        $totalUsers = $users->count();
        $aktif      = $users->where('is_active', 1)->count();
        $nonAktif   = $users->where('is_active', 0)->count();

        return view('admin.user.index', compact('users', 'totalUsers', 'aktif', 'nonAktif'));
    }

    public function create()
    {
        $cabangs = DB::table('tb_cabang')->get();
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

        $cabangs = DB::table('tb_cabang')->get();
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

        return redirect()->route('admin.user.index')->with('success', 'Data user diperbarui!');
    }

    public function destroy($id)
    {
        // ✅ tidak bisa hapus akun sendiri
        if ($id == Auth::user()->id_user) {
            return redirect()->route('admin.user.index')
                ->with('error', 'Tidak bisa menghapus akun sendiri!');
        }

        // ✅ tidak bisa hapus user yang masih punya transaksi
        $adaTransaksi = DB::table('tb_penjualan')->where('id_user', $id)->exists();
        if ($adaTransaksi) {
            return redirect()->route('admin.user.index')
                ->with('error', 'User tidak bisa dihapus karena masih memiliki data transaksi!');
        }

        DB::table('tb_user')->where('id_user', $id)->delete();
        return redirect()->route('admin.user.index')->with('success', 'User telah dihapus!');
    }
}