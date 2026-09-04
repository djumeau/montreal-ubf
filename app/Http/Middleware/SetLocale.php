<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Look for the 'locale' cookie, fall back to the app default config
        $locale = Cookie::get('locale', config('app.locale'));

        // 2. Set the application language
        App::setLocale($locale);

        return $next($request);
    }
}
