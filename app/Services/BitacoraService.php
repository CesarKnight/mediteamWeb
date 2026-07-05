<?php

namespace App\Services;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraService
{
    public function __construct(
        private readonly Request $request,
    ) {
    }

    public function registrar(string $descripcion, string $controlador): Bitacora
    {
        return Bitacora::create([
            'user_id'     => $this->request->user()?->id,
            'descripcion' => $descripcion,
            'controlador' => $controlador,
            'ip'          => $this->request->ip(),
        ]);
    }
}
