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

        // Cross-Origin Opener Policy (COOP) - Isolasi origin tingkat window untuk keamanan dan kepatuhan Lighthouse
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // Cross-Origin Resource Policy (CORP)
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        // Strict-Transport-Security (HSTS) jika diakses via HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Hapus header X-Powered-By (jangan ekspos info framework/PHP)
        $response->headers->remove('X-Powered-By');

        // Permissions Policy (batasi akses ke fitur browser sensitif)
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=()'
        );

        // Content Security Policy (CSP) dalam Enforcement Mode
        // - Di development ('local'): Izinkan localhost, IP lokal, Vite HMR websocket, tanpa memicu peringatan Lighthouse
        // - Di production: Pembatasan ketat untuk aset mandiri dan CDN terpercaya
        if (app()->environment('local')) {
            $csp = implode('; ', [
                "default-src 'self' 'unsafe-inline' 'unsafe-eval' http: https: ws: wss: data: blob:",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' http: https:",
                "style-src 'self' 'unsafe-inline' http: https:",
                "font-src 'self' data: http: https:",
                "img-src 'self' data: blob: http: https:",
                "connect-src 'self' http: https: ws: wss:",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "form-action 'self'",
            ]);
        } else {
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com",
                "font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com data:",
                "img-src 'self' data: blob:",
                "connect-src 'self'",
                "frame-ancestors 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
            ]);
        }
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
