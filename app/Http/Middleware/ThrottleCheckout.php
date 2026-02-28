<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleCheckout
{
    public const MAX_ATTEMPTS = 5;
    public const DECAY_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'error' => 'Too many checkout attempts. Please try again later.',
                'retry_after' => $seconds,
            ], 429);
        }

        RateLimiter::hit($key, self::DECAY_SECONDS);

        return $next($request);
    }

    protected function resolveRequestSignature(Request $request): string
    {
        if ($request->user()) {
            return 'checkout:' . $request->user()->id;
        }

        return 'checkout:' . $request->ip();
    }
}
