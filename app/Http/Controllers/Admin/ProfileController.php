<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'kasir') {
            return view('kasir.profile.index', compact('user'));
        }

        return view('admin.profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // ✅ validasi nama dan username
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:100|unique:tb_user,username,' . $user->id_user . ',id_user',
        ]);

        $user->update([
            'name'     => $request->name,
            'username' => $request->username,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Kasir: tidak perlu password lama (lebih simple)
        // Admin: wajib memasukkan password lama untuk keamanan
        if ($user->role === 'admin') {
            $request->validate([
                'current_password' => 'required',
                'password'         => 'required|min:6|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password lama tidak cocok!');
            }
        } else {
            // Kasir
            $request->validate([
                'password' => 'required|min:6|confirmed',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Perbarui hash sesi saat ini agar user sendiri tidak ter-logout
        session(['auth_password_hash' => $user->password]);

        return back()->with('success', 'Password berhasil diganti!');
    }
}