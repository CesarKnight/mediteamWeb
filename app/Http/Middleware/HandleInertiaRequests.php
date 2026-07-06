<?php

namespace App\Http\Middleware;

use App\Services\VisitaService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(private readonly VisitaService $visitas) {}

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'permisos' => $request->user()?->rol?->permisos->pluck('nombre') ?? [],
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'visitasPagina' => $this->visitasDeEstaPagina($request),
        ];
    }

    /**
     * Counts this hit and returns the page's up-to-date total, for display
     * in a per-page footer. Only named GET/HEAD routes count as "a page" —
     * form submissions (POST/PUT/PATCH/DELETE) and unnamed routes don't.
     */
    private function visitasDeEstaPagina(Request $request): ?int
    {
        if (! $request->isMethod('get')) {
            return null;
        }

        $ruta = $request->route()?->getName();

        return $ruta === null ? null : $this->visitas->registrarYContar($ruta);
    }
}
