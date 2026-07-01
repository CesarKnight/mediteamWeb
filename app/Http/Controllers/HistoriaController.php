<?php

namespace App\Http\Controllers;

use App\Models\Historia;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class HistoriaController extends Controller
{
    private function formatHistoria(Historia $h): array
    {
        return [
            'id'     => $h->id,
            'estado' => $h->estado,
            'paciente' => [
                'id'       => $h->paciente->id,
                'name'     => $h->paciente->user->name,
                'lastName' => $h->paciente->user->lastName,
            ],
            'medicoCreador' => [
                'id'           => $h->medicoCreador->id,
                'name'         => $h->medicoCreador->user->name,
                'lastName'     => $h->medicoCreador->user->lastName,
                'especialidad' => $h->medicoCreador->especialidad,
            ],
            'medicosInvolucrados' => $h->medicosInvolucrados->map(fn($m) => [
                'id'       => $m->id,
                'name'     => $m->user->name,
                'lastName' => $m->user->lastName,
            ]),
            'consultas' => $h->consultas->map(fn($c) => [
                'id'     => $c->id,
                'motivo' => $c->motivo,
                'peso'   => $c->peso,
                'altura' => $c->altura,
            ]),
        ];
    }

    public function index(): Response
    {
        $historias = Historia::with([
            'paciente.user',
            'medicoCreador.user',
            'medicosInvolucrados.user',
            'consultas',
        ])->get()->map(fn($h) => $this->formatHistoria($h));

        return Inertia::render('historias/index', [
            'historias' => $historias,
        ]);
    }

    public function show(Historia $historia): Response
    {
        $historia->load([
            'paciente.user',
            'medicoCreador.user',
            'medicosInvolucrados.user',
            'consultas'
        ]);

        return Inertia::render('historias/show', [
            'historia' => $this->formatHistoria($historia),
            'pacientes' => Paciente::with('user')->get()->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->user->name,
                'lastName' => $p->user->lastName,
            ]),
            'medicos' => Medico::with('user')->get()->map(fn($m) => [
                'id'           => $m->id,
                'name'         => $m->user->name,
                'lastName'     => $m->user->lastName,
                'especialidad' => $m->especialidad,
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('historias/create', [
            'pacientes' => Paciente::with('user')->get()->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->user->name,
                'lastName' => $p->user->lastName,
            ]),
            'medicos' => Medico::with('user')->get()->map(fn($m) => [
                'id'           => $m->id,
                'name'         => $m->user->name,
                'lastName'     => $m->user->lastName,
                'especialidad' => $m->especialidad,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'paciente_id'            => ['required', 'exists:pacientes,id'],
            'medico_id'              => ['required', 'exists:medicos,id'],
            'estado'                 => ['required', 'string'],
            'medicos_involucrados'   => ['array'],
            'medicos_involucrados.*' => ['exists:medicos,id'],
        ])->validate();

        $historia = Historia::create([
            'estado'     => $validated['estado'],
            'paciente_id' => $validated['paciente_id'],
            'medico_id'   => $validated['medico_id'],
        ]);

        if (!empty($validated['medicos_involucrados'])) {
            $historia->medicosInvolucrados()->attach($validated['medicos_involucrados']);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Historia clínica creada exitosamente.')]);

        return to_route('Historiasindex');
    }

    public function update(Request $request, Historia $historia): RedirectResponse
    {
        $historia->load('medicosInvolucrados');

        $validated = Validator::make($request->all(), [
            'paciente_id'             => ['required', 'exists:pacientes,id'],
            'medico_id'               => ['required', 'exists:medicos,id'],
            'estado'                  => ['required', 'string'],
            'medicos_involucrados'    => ['array'],
            'medicos_involucrados.*'  => ['exists:medicos,id'],
        ])->validate();

        $historia->update([
            'estado'      => $validated['estado'],
            'paciente_id' => $validated['paciente_id'],
            'medico_id'   => $validated['medico_id'],
        ]);

        // sync() replaces all pivot rows with the new set in one call
        $historia->medicosInvolucrados()->sync($validated['medicos_involucrados'] ?? []);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Historia clínica actualizada exitosamente.')]);

        return to_route('Historiasindex');
    }

    public function destroy(Request $request, Historia $historia)
    {
        $historia->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Historia eliminada exitosamente.')]);

        return to_route('Historiasindex');
    }
}
