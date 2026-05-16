<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', 'fr');
        if (! in_array($locale, ['en', 'fr', 'ar'], true)) {
            $locale = 'fr';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
