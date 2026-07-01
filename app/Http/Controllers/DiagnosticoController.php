<?php

namespace App\Http\Controllers;

use App\Models\Diagnostico;
use App\Models\Historia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class DiagnosticoController extends Controller
{
    public function store(Request $request, Historia $historia): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'diagnostico' => ['required', 'string'],
            'enfermedad'  => ['required', 'string'],
            'gravedad'    => ['required', 'string', 'in:leve,medio,severo'],
        ])->validate();

        $historia->diagnosticos()->create($validated);

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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Diagnóstico actualizado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function destroy(Historia $historia, Diagnostico $diagnostico): RedirectResponse
    {
        $diagnostico->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Diagnóstico eliminado exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }
}