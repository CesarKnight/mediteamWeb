<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Historia;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ConsultaController extends Controller
{
    public function __construct(private readonly BitacoraService $bitacora) {}

    public function store(Request $request, Historia $historia): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'motivo' => ['required', 'string'],
            'peso'   => ['required', 'numeric'],
            'altura' => ['required', 'numeric'],
        ])->validate();

        $consulta = $historia->consultas()->create($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " registró la consulta #{$consulta->id} en la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Consulta registrada exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function update(Request $request, Historia $historia, Consulta $consulta): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'motivo' => ['required', 'string'],
            'peso'   => ['required', 'numeric'],
            'altura' => ['required', 'numeric'],
        ])->validate();

        $consulta->update($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó la consulta #{$consulta->id} de la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Consulta actualizada exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function destroy(Historia $historia, Consulta $consulta): RedirectResponse
    {
        $consultaId = $consulta->id;

        $consulta->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó la consulta #{$consultaId} de la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Consulta eliminada exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }
}