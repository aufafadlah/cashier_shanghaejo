<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // ✅ validasi input
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // ✅ rate limiting — maksimal 5 percobaan per menit
        $key = 'login.' . Str::lower($request->username) . '.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.");
        }

        if (Auth::attempt([
            'username'  => $request->username,
            'password'  => $request->password,
            'is_active' => 1
        ])) {
            // ✅ reset rate limiter setelah login berhasil
            RateLimiter::clear($key);

            $request->session()->regenerate();

            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('kasir.pesan.index');
        }

        // ✅ hitung percobaan gagal
        RateLimiter::hit($key, 60); // 60 detik

        return back()->with('error', 'Username/Password salah atau akun tidak aktif.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}