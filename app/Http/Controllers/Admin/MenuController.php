<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index()
    {
        $menus = DB::table('tb_menu')->get();
        $totalMenu = $menus->count();
        $aktif = $menus->where('is_active', 1)->count();
        $nonAktif = $menus->where('is_active', 0)->count(); 

        return view('admin.menu.index', compact('menus', 'totalMenu', 'aktif', 'nonAktif'));
    }

    public function create()
    {
        return view('admin.menu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_menu'  => 'required',
            'harga_menu' => 'required|numeric',
            'is_active'  => 'required'
        ]);

        DB::table('tb_menu')->insert([
            'nama_menu'  => $request->nama_menu,
            'harga_menu' => $request->harga_menu,
            'is_active'  => $request->is_active,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambah!');
    }

    public function edit($id)
    {
        $menu = DB::table('tb_menu')->where('id_menu', $id)->first();
        
        if (!$menu) {
            return redirect()->route('admin.menu.index')->with('error', 'Data tidak ditemukan!');
        }

        // Memanggil file edit.blade.php
        return view('admin.menu.edit', compact('menu')); 
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_menu'  => 'required',
            'harga_menu' => 'required|numeric',
            'is_active'  => 'required',
        ]);

        DB::table('tb_menu')->where('id_menu', $id)->update([
            'nama_menu'  => $request->nama_menu,
            'harga_menu' => $request->harga_menu,
            'is_active'  => $request->is_active,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('tb_menu')->where('id_menu', $id)->delete();
        return redirect()->route('admin.menu.index')->with('success', 'menu telah dihapus!');
    }
}