<?php

namespace App\Services;

use App\Models\Visita;
use Illuminate\Database\QueryException;

class VisitaService
{
    /**
     * Atomically bumps the visit counter for a route name and returns the
     * fresh total, so the very page being rendered can display its own
     * up-to-date count (e.g. in a footer). The UPDATE itself is a single
     * "visitas = visitas + 1" statement, so concurrent requests for the
     * same page can never clobber each other's increment.
     *
     * Routes are normally pre-seeded (see VisitaSeeder) so the increment
     * branch is the only one that runs in practice; the firstOrCreate
     * fallback just makes newly-added routes track themselves the first
     * time they're hit, without needing a reseed.
     */
    public function registrarYContar(string $ruta): int
    {
        $actualizados = Visita::where('ruta', $ruta)->increment('visitas', 1, [
            'ultima_visita_at' => now(),
        ]);

        if ($actualizados === 0) {
            try {
                Visita::firstOrCreate(
                    ['ruta' => $ruta],
                    ['visitas' => 1, 'ultima_visita_at' => now()],
                );
            } catch (QueryException) {
                // Lost a race against another request creating the same
                // brand-new route's row first — fall back to the normal
                // atomic increment.
                Visita::where('ruta', $ruta)->increment('visitas', 1, [
                    'ultima_visita_at' => now(),
                ]);
            }
        }

        return Visita::where('ruta', $ruta)->value('visitas') ?? 0;
    }
}
