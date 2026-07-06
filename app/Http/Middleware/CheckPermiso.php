<?php

namespace App\Http\Middleware;

use App\Enums\PermisoEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    /**
     * Abort with 403 unless the authenticated user's role has the given
     * PermisoEnum value. Usage in routes: ->middleware('permiso:Usuario.crear')
     */
    public function handle(Request $request, Closure $next, string $permiso): Response
    {
        abort_unless(
            $request->user()?->tienePermiso(PermisoEnum::from($permiso)) ?? false,
            403,
        );

        return $next($request);
    }
}
