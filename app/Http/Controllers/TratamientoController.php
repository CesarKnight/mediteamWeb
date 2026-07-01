<?php

namespace App\Http\Controllers;

use App\Models\Historia;
use App\Models\Tratamiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class TratamientoController extends Controller
{
    public function store(Request $request, Historia $historia): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'medicamento'      => ['required', 'string'],
            'frecuencia_horas' => ['required', 'numeric'],
        ])->validate();

        $historia->tratamientos()->create($validated);

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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tratamiento actualizado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function destroy(Historia $historia, Tratamiento $tratamiento): RedirectResponse
    {
        $tratamiento->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Tratamiento eliminado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }
}