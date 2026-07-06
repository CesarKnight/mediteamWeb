<?php

namespace Database\Seeders;

use App\Models\Visita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class VisitaSeeder extends Seeder
{
    /**
     * Pre-populates one row per named GET/HEAD route so every page in the
     * app shows up on the visitas listing with a 0 count from day one,
     * instead of only appearing after its first hit.
     */
    public function run(): void
    {
        collect(Route::getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods(), true) && $route->getName() !== null)
            ->map(fn ($route) => $route->getName())
            ->unique()
            ->each(fn (string $nombre) => Visita::firstOrCreate(
                ['ruta' => $nombre],
                ['visitas' => 0],
            ));
    }
}
