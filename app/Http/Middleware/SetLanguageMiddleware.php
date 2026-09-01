<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get language from query param or header
        // $lang = $request->query('lang') ?? $request->header('Accept-Language', 'en');

        // 1️⃣ Auth user language (highest priority)
        if (auth('api')->check()) {
            $lang = auth('api')->user()->language;
        }
        // 2️⃣ Query param
        elseif ($request->query('lang')) {
            $lang = $request->query('lang');
        }
        // 3️⃣ Header
        else {
            $lang = $request->header('Accept-Language', 'en');
        }

        // Only allow 'en' or 'it'
        if (!in_array($lang, ['en', 'it'])) {
            $lang = 'en';
        }

        // Merge into request
        $request->merge(['language_code' => $lang]);

        // app()->setLocale($lang); // 🔥 Laravel localization

        return $next($request);
    }
}
