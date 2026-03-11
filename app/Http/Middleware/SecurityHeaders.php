<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!app()->environment('local')) {
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
            $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

            if ($request->isSecure()) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            }

            $csp = "default-src 'self'; "
                . "script-src 'self' 'unsafe-inline' https:; "
                . "style-src 'self' 'unsafe-inline' https:; "
                . "img-src 'self' data: blob: https:; "
                . "font-src 'self' data: https:; "
                . "connect-src 'self' https:; "
                . "frame-ancestors 'self'; base-uri 'self'; form-action 'self'";

            if (config('app.debug')) {
                $response->headers->set('Content-Security-Policy-Report-Only', $csp);
            } else {
                $response->headers->set('Content-Security-Policy', $csp);
            }
        }

        $this->setCacheHeaders($request, $response);

        return $response;
    }

    private function setCacheHeaders(Request $request, Response $response): void
    {
        $path = $request->getPathInfo();
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        $cacheExtensions = ['css', 'js', 'woff2', 'avif', 'webp', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico'];

        if (in_array($extension, $cacheExtensions)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } elseif ($request->isMethod('GET')) {
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        }
    }
}
