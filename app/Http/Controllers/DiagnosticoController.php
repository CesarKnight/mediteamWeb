<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\Historia;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DiagnosticoController extends Controller
{
    public function __construct(private readonly BitacoraService $bitacora) {}

    public function store(Request $request, Historia $historia): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'diagnostico' => ['required', 'string'],
            'enfermedad'  => ['required', 'string'],
            'gravedad'    => ['required', 'string', 'in:leve,medio,severo'],
        ])->validate();

        $diagnostico = $historia->diagnosticos()->create($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " registró el diagnóstico #{$diagnostico->id} en la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Diagnóstico registrado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function update(Request $request, Historia $historia, Diagnostico $diagnostico): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'diagnostico' => ['required', 'string'],
            'enfermedad'  => ['required', 'string'],
            'gravedad'    => ['required', 'string', 'in:leve,medio,severo'],
        ])->validate();

        $diagnostico->update($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó el diagnóstico #{$diagnostico->id} de la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Diagnóstico actualizado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function destroy(Historia $historia, Diagnostico $diagnostico): RedirectResponse
    {
        $diagnosticoId = $diagnostico->id;

        $diagnostico->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó el diagnóstico #{$diagnosticoId} de la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Diagnóstico eliminado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }
}