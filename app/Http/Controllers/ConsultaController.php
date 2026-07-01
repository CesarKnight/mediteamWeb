<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Historia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ConsultaController extends Controller
{
    private function formatConsulta(Consulta $c): array
    {
        return [
            'id'           => $c->id,
            'motivo'       => $c->motivo,
            'peso'         => $c->peso,
            'altura'       => $c->altura,
            'fecha_creacion' => $c->created_at,
            'historia_id'  => $c->historia_id,
        ];
    }

    public function create(Historia $historia): Response
    {
        return Inertia::render('consultas/create', [
            'historia' => [
                'id' => $historia->id,
                'estado' => $historia->estado,
            ],
        ]);
    }

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

    public function show(Historia $historia, Consulta $consulta): Response
    {
        return Inertia::render('consultas/show', [
            'historia' => [
                'id' => $historia->id,
            ],
            'consulta' => $this->formatConsulta($consulta),
        ]);
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