<?php

namespace App\Http\Controllers;

use App\Models\Historia;
use App\Models\Tratamiento;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TratamientoController extends Controller
{
    public function __construct(private readonly BitacoraService $bitacora) {}

    public function store(Request $request, Historia $historia): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'medicamento'      => ['required', 'string'],
            'frecuencia_horas' => ['required', 'numeric'],
        ])->validate();

        $tratamiento = $historia->tratamientos()->create($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " registró el tratamiento #{$tratamiento->id} en la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tratamiento registrado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function update(Request $request, Historia $historia, Tratamiento $tratamiento): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'medicamento'      => ['required', 'string'],
            'frecuencia_horas' => ['required', 'numeric'],
        ])->validate();

        $tratamiento->update($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó el tratamiento #{$tratamiento->id} de la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tratamiento actualizado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function destroy(Historia $historia, Tratamiento $tratamiento): RedirectResponse
    {
        $tratamientoId = $tratamiento->id;

        $tratamiento->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó el tratamiento #{$tratamientoId} de la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tratamiento eliminado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }
}