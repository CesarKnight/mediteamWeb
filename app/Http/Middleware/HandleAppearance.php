<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        View::share('appearance', $request->cookie('appearance') ?? 'system');
        View::share('themeProfile', $request->cookie('theme_profile') ?? 'adultos');
        View::share('fontFamily', $request->cookie('font_family') ?? 'classic');
        View::share('fontSize', $request->cookie('font_size') ?? 'medium');
        View::share('contrast', $request->cookie('contrast') ?? 'normal');
        View::share('colorScheme', $request->cookie('color_scheme') ?? 'teal');

        return $next($request);
    }
}
