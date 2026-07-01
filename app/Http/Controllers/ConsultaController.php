<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Historia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ConsultaController extends Controller
{

    public function store(Request $request, Historia $historia): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'motivo' => ['required', 'string'],
            'peso'   => ['required', 'numeric'],
            'altura' => ['required', 'numeric'],
        ])->validate();

        $historia->consultas()->create($validated);

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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Consulta actualizada exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }

    public function destroy(Historia $historia, Consulta $consulta): RedirectResponse
    {
        $consulta->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Consulta eliminada exitosamente.')]);

        return to_route('Historiasshow', $historia);
    }
}