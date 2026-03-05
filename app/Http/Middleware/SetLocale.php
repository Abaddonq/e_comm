<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set the application locale based on cookie, browser preference, or default.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $available = config('app.available_locales', ['tr', 'en']);
        $locale = null;

        // 1. Check cookie preference
        $cookieLocale = $request->cookie('locale');
        if ($cookieLocale && in_array($cookieLocale, $available, true)) {
            $locale = $cookieLocale;
        }

        // 2. Detect browser language on first visit
        if (!$locale) {
            $browserLocale = $this->detectBrowserLocale($request, $available);
            if ($browserLocale) {
                $locale = $browserLocale;
            }
        }

        // 3. Fall back to app default
        $locale = $locale ?? config('app.locale', 'tr');

        app()->setLocale($locale);

        return $next($request);
    }

    /**
     * Parse Accept-Language header and return the best matching locale.
     */
    private function detectBrowserLocale(Request $request, array $available): ?string
    {
        $acceptLanguage = $request->header('Accept-Language', '');

        if (empty($acceptLanguage)) {
            return null;
        }

        // Parse "en-US,en;q=0.9,tr;q=0.8" into sorted preferences
        $languages = [];
        foreach (explode(',', $acceptLanguage) as $part) {
            $part = trim($part);
            if (str_contains($part, ';q=')) {
                [$lang, $q] = explode(';q=', $part);
                $languages[trim($lang)] = (float) $q;
            } else {
                $languages[$part] = 1.0;
            }
        }

        arsort($languages);

        foreach ($languages as $lang => $q) {
            // Check exact match first (e.g. "tr")
            $short = strtolower(substr($lang, 0, 2));
            if (in_array($short, $available, true)) {
                return $short;
            }
        }

        return null;
    }
}
