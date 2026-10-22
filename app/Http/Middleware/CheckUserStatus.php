<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user()->fresh(); // Ambil data paling fresh dari database

            $errorMessage = null;

            // 1. Cek apakah user telah dihapus atau dinonaktifkan
            if (!$user || !$user->is_active) {
                $errorMessage = 'Akun Anda telah dinonaktifkan atau dihapus. Silakan hubungi Administrator.';
            }

            // 2. Cek apakah cabang kasir dinonaktifkan (admin tetap bisa akses untuk mengelola kembali)
            if (!$errorMessage && $user->role === 'kasir' && $user->id_cabang) {
                $cabang = DB::table('tb_cabang')->where('id_cabang', $user->id_cabang)->first();
                if (!$cabang || !$cabang->is_active) {
                    $errorMessage = 'Cabang Anda sedang dinonaktifkan. Silakan hubungi Administrator.';
                }
            }

            // 3. Cek apakah password telah diubah sejak login
            $savedHash = session('auth_password_hash');
            if (!$errorMessage && $savedHash && $user->password !== $savedHash) {
                $errorMessage = 'Password akun Anda telah diubah. Silakan login kembali dengan password baru.';
            }

            // Jika ada pelanggaran status, paksa logout saat itu juga
            if ($errorMessage) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Jika request berupa AJAX/Fetch (seperti add to cart, checkout, atau polling realtime)
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success'  => false,
                        'message'  => $errorMessage,
                        'redirect' => route('login'),
                    ], 401);
                }

                return redirect()->route('login')->with('error', $errorMessage);
            }
        }

        return $next($request);
    }
}
