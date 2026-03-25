<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   public function index()
{
    $users = DB::table('tb_user')
        ->leftJoin('tb_cabang', 'tb_user.id_cabang', '=', 'tb_cabang.id_cabang')
        ->select('tb_user.*', 'tb_cabang.nama_cabang')
        ->get();

    $totalUsers = $users->count();
    $aktif = $users->where('is_active', 1)->count();
    $nonAktif = $users->where('is_active', 0)->count();

    return view('admin.user.index', compact('users', 'totalUsers', 'aktif', 'nonAktif'));
}

    /**
     * Tampilkan Form Tambah User
     */
    public function create()
    {
        // AMBIL DATA CABANG AGAR TIDAK ERROR "Undefined variable $cabangs"
        $cabangs = DB::table('tb_cabang')->get(); 

        return view('admin.user.create', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cabang' => 'required',
            'name'      => 'required',
            'username'  => 'required|unique:tb_user,username',
            'password'  => 'required|min:6',
            'role'      => 'required',
            'is_active' => 'required|in:0,1',
        ]);

        DB::table('tb_user')->insert([
            'id_cabang'   => $request->id_cabang, // Gunakan input dari form, bukan hardcode 1
            'name'        => $request->name,
            'username'    => $request->username,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'is_active'   => $request->is_active ?? 1, 
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Tampilkan Form Edit User
     */
    public function edit($id)
    {
        $user = DB::table('tb_user')->where('id_user', $id)->first();
        
        // AMBIL DATA CABANG JUGA DI SINI UNTUK DROPDOWN EDIT
        $cabangs = DB::table('tb_cabang')->get(); 

        if (!$user) {
            return redirect()->route('admin.user.index')->with('error', 'User tidak ditemukan');
        }

        return view('admin.user.edit', compact('user', 'cabangs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_cabang'=> 'required',
            'name'     => 'required',
            'username' => 'required|unique:tb_user,username,'.$id.',id_user',
            'role'     => 'required',
            'is_active' => 'required',
        ]);

        $data = [
            'id_cabang'  => $request->id_cabang,
            'name'       => $request->name,
            'username'   => $request->username,
            'role'       => $request->role,
            'is_active'  => $request->is_active,
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
        DB::table('tb_user')->where('id_user', $id)->delete();
        return redirect()->route('admin.user.index')->with('success', 'User telah dihapus!');
    }
}