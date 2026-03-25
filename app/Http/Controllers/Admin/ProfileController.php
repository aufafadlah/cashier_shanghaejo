<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::first(); // Sementara pakai first() agar tidak null
        return view('admin.profile.index', compact('user'));
    }

    // FUNGSI UNTUK SIMPAN NAMA & USERNAME
    public function update(Request $request)
    {
        $user = User::first(); // Sesuaikan nanti dengan Auth::user()
        
        $user->update([
            'name'     => $request->name, // Sesuai <input name="nama"> di Blade kamu
            'username' => $request->username,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // FUNGSI UNTUK SIMPAN PASSWORD BARU DARI MODAL
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed', // 'confirmed' otomatis cek password_confirmation
        ]);

        $user = User::first(); 
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diganti!');
    }
}