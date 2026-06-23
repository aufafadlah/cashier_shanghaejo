<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- WAJIB TAMBAHKAN INI

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Gunakan Auth::check() untuk memastikan user sudah login
        // Gunakan Auth::user() untuk mengambil data user
        if (!Auth::check() || Auth::user()->role !== $role) {
            return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}