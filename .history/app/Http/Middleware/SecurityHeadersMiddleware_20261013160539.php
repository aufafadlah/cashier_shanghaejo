<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeadersMiddleware
 * 
 * Menambahkan HTTP security headers standar industri pada setiap response
 * untuk melindungi aplikasi dari serangan XSS, Clickjacking, MIME sniffing,
 * dan kebocoran informasi referrer.
 */
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Lewati header security untuk response non-HTML (download, JSON API, dsb)
        $contentType = $response->headers->get('Content-Type', '');
        $isHtml = str_contains($contentType, 'text/html') || $contentType === '';
        if (!$isHtml) {
            return $response;
        }

        // Mencegah halaman dimuat dalam <iframe> (anti-Clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Mencegah browser menebak-nebak tipe konten (anti-MIME sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Aktifkan proteksi XSS bawaan browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Hanya kirim referrer URL asal saat navigasi ke domain yang sama
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Hapus header X-Powered-By (jangan ekspos info framework/PHP)
        $response->headers->remove('X-Powered-By');

        // Permissions Policy (batasi akses ke fitur browser sensitif)
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        // Content Security Policy (CSP)
        // - 'unsafe-inline' & 'unsafe-eval' diperlukan untuk Alpine.js & Vite dev HMR
        // - ws: & wss: diperlukan untuk Vite HMR WebSocket di development
        // - SweetAlert2 & Font Awesome dari CDN diizinkan secara eksplisit
        $appUrl = config('app.url', 'http://localhost');
        $vitePort = 5173; // Port default Vite dev server

$csp = implode('; ', [
    "default-src 'self'",
    // Tambahkan http://localhost:5173 dan ws://localhost:5173 untuk Vite
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173 https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
    "style-src 'self' 'unsafe-inline' http://localhost:5173 https://cdnjs.cloudflare.com https://fonts.googleapis.com",
    "font-src 'self' http://localhost:5173 https://cdnjs.cloudflare.com https://fonts.gstatic.com",
    "img-src 'self' data: blob: http://localhost:5173",
    "connect-src 'self' http://localhost:5173 ws://localhost:5173", // Penting untuk Hot Module Replacement (HMR) Vite
    "frame-ancestors 'self'",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
