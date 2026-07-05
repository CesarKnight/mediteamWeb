<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Inertia\Inertia;
use Inertia\Response;

class BitacoraController extends Controller
{
    public function index(): Response
    {
        $bitacoras = Bitacora::with('user')
            ->latest()
            ->get()
            ->map(fn (Bitacora $b) => [
                'id'          => $b->id,
                'descripcion' => $b->descripcion,
                'controlador' => $b->controlador,
                'ip'          => $b->ip,
                'created_at'  => $b->created_at,
                'user'        => $b->user ? [
                    'name'     => $b->user->name,
                    'lastName' => $b->user->lastName,
                ] : null,
            ]);

        return Inertia::render('bitacora/index', [
            'bitacoras' => $bitacoras,
        ]);
    }
}
