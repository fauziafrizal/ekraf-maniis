<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class BrowserSecurity
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Vite::useCspNonce();
        $oldSecure = config('session.secure');
        if ($request->isSecure()) {
            config(['session.secure' => true]);
        }
        try {
            $response = $next($request);
        } finally {
            config(['session.secure' => $oldSecure]);
        }
        if ($request->isSecure() && config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(self), geolocation=(), payment=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $type = (string) $response->headers->get('Content-Type');
        if (str_contains($type, 'text/html') || str_contains($type, 'application/json')) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }
        if ($request->is('pengurus', 'pengurus/*', 'verifikasi/*')) {
            $response->headers->set('Cache-Control', 'no-store, private');
            $response->headers->set('Referrer-Policy', 'no-referrer');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }
        if (str_contains($type, 'text/html') && ! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'nonce-".$nonce."'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob: https://tile.openstreetmap.org; font-src 'self'; media-src 'self' blob:; connect-src 'self' https://tile.openstreetmap.org; worker-src 'self' blob:; object-src 'none'; frame-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'");
        }

        return $response;
    }
}
