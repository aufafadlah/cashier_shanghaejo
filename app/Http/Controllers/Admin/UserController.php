<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash; // Penting untuk password user

class UserController extends Controller
{
    /**
     * Tampilkan Halaman List User
     * URL: admin/user
     */
    public function index()
    {
        // Ambil data dari tabel tb_user
        $users = DB::table('tb_user')->get();

        // Hitung statistik untuk dashboard atas
        $totalUsers = $users->count();
        $totalAktif = $users->where('is_active', 1)->count();
        $totalKasir = $users->where('role', 'kasir')->count();

        // Mengarah ke view: resources/views/user/index.blade.php
        return view('admin.user.index', compact(
            'users',
            'totalUsers',
            'totalAktif',
            'totalKasir'
        ));
    }

    /**
     * Tampilkan Form Tambah User
     */
    public function create()
    {
        // Sesuaikan path view jika folder kamu di resources/views/user/create.blade.php
        return view('admin.user.create');
    }

    /**
     * Proses Simpan User Baru
     */
   public function store(Request $request)
{
    // 1. Tambahkan 'is_active' ke dalam validasi
    $request->validate([
        'name'      => 'required',
        'username'  => 'required|unique:tb_user,username',
        'password'  => 'required|min:6',
        'role'      => 'required',
        'is_active' => 'required|in:0,1', // Pastikan input status ada dan nilainya 0 atau 1
    ]);

    DB::table('tb_user')->insert([
        'name'        => $request->name,
        'username'    => $request->username,
        'password'    => Hash::make($request->password),
        'role'        => $request->role,
        'id_cabang'   => 1, 
        // Pakai null coalescing (??) agar jika input kosong, otomatis jadi 1 (Aktif)
        'is_active'   => $request->is_active ?? 1, 
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
}
    /**
     * Tampilkan Form Edit User
     */
    public function edit($id)
    {
        // Gunakan id_user sesuai Primary Key di DB kamu
        $user = DB::table('tb_user')->where('id_user', $id)->first();

        if (!$user) {
            return redirect()->route('admin.user.index')->with('error', 'User tidak ditemukan');
        }

        return view('admin.user.edit', compact('user'));
    }

    /**
     * Proses Update Data User
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'     => 'required',
            'username' => 'required|unique:tb_user,username,'.$id.',id_user',
            'role'     => 'required',
        ]);

        $data = [
            'name'       => $request->name,
            'username'   => $request->username,
            'role'       => $request->role,
            'is_active'  => $request->is_active,
            'updated_at' => now(),
        ];

        // Jika password diisi, maka update passwordnya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('tb_user')->where('id_user', $id)->update($data);

        return redirect()->route('user.index')->with('success', 'Data user diperbarui!');
    }

    /**
     * Hapus User
     */
    public function destroy($id)
    {
        DB::table('tb_user')->where('id_user', $id)->delete();
        return redirect()->route('user.index')->with('success', 'User telah dihapus!');
    }
}