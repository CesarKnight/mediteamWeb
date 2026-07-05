<?php

namespace App\Http\Controllers;

use App\Models\Historia;
use App\Models\Medico;
use App\Models\Paciente;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class HistoriaController extends Controller
{
    public function __construct(private readonly BitacoraService $bitacora) {}

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
                'created_at' => $c->created_at,
            ]),
            'diagnosticos' => $h->diagnosticos->map(fn($d) => [
                'id'          => $d->id,
                'diagnostico' => $d->diagnostico,
                'enfermedad'  => $d->enfermedad,
                'gravedad'    => $d->gravedad,
                'created_at'  => $d->created_at,
            ]),
            'tratamientos' => $h->tratamientos->map(fn($t) => [
                'id'               => $t->id,
                'medicamento'      => $t->medicamento,
                'frecuencia_horas' => $t->frecuencia_horas,
                'created_at'       => $t->created_at,
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
            'diagnosticos',
            'tratamientos'
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
            'consultas',
            'diagnosticos',
            'tratamientos'
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

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " creó la historia clínica #{$historia->id} para el paciente con id {$validated['paciente_id']}.",
            static::class,
        );

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

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó la historia clínica #{$historia->id}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Historia clínica actualizada exitosamente.')]);

        return to_route('Historiasindex');
    }

    public function destroy(Request $request, Historia $historia)
    {
        $historiaId = $historia->id;

        $historia->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó la historia clínica #{$historiaId}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Historia eliminada exitosamente.')]);

        return to_route('Historiasindex');
    }
}
