<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    public function __construct(private readonly BitacoraService $bitacora) {}

    private function formatCita(Cita $c): array
    {
        return [
            'id'          => $c->id,
            'estado'      => $c->estado,
            'hora_inicio' => $c->hora_inicio,
            'hora_fin'    => $c->hora_fin,
            'paciente' => [
                'id'       => $c->paciente->id,
                'name'     => $c->paciente->user->name,
                'lastName' => $c->paciente->user->lastName,
            ],
            'medico' => [
                'id'           => $c->medico->id,
                'name'         => $c->medico->user->name,
                'lastName'     => $c->medico->user->lastName,
                'especialidad' => $c->medico->especialidad,
            ],
            'servicio' => [
                'id'       => $c->servicio->id,
                'titulo'   => $c->servicio->titulo,
                'duracion' => $c->servicio->duracion,
                'precio'   => $c->servicio->precio,
            ],
        ];
    }

    private function hasOverlap(int $medicoId, string $horaInicio, string $horaFin, ?int $excludeCitaId = null): bool
    {
        return Cita::where('medico_id', $medicoId)
            ->where('estado', '!=', 'cancelado')
            ->when($excludeCitaId, fn($query) => $query->where('id', '!=', $excludeCitaId))
            ->where('hora_inicio', '<', $horaFin)
            ->where('hora_fin', '>', $horaInicio)
            ->exists();
    }

    public function index(): Response
    {
        $citas = Cita::with(['paciente.user', 'medico.user', 'servicio'])
            ->orderBy('hora_inicio')
            ->get()
            ->map(fn($c) => $this->formatCita($c));

        return Inertia::render('citas/index', [
            'citas' => $citas,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('citas/create', [
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
            'servicios' => Servicio::where('estado', 'disponible')->get()->map(fn($s) => [
                'id'       => $s->id,
                'titulo'   => $s->titulo,
                'duracion' => $s->duracion,
                'precio'   => $s->precio,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id'   => ['required', 'exists:medicos,id'],
            'servicio_id' => ['required', 'exists:servicios,id'],
            'hora_inicio' => ['required', 'date'],
            'hora_fin'    => ['required', 'date', 'after:hora_inicio'],
            'estado'      => ['required', 'string', 'in:aprobado,pospuesto,cancelado'],
        ]);

        $validator->after(function ($validator) {
            $data = $validator->getData();
            if (
                !empty($data['medico_id']) && !empty($data['hora_inicio']) && !empty($data['hora_fin'])
                && $this->hasOverlap($data['medico_id'], $data['hora_inicio'], $data['hora_fin'])
            ) {
                $validator->errors()->add('hora_inicio', 'El médico ya tiene una cita agendada en ese horario.');
            }
        });

        $validated = $validator->validate();

        $cita = Cita::create($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " agendó la cita #{$cita->id} para el paciente con id {$validated['paciente_id']} con el médico con id {$validated['medico_id']}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Cita agendada exitosamente.')]);

        return to_route('Citasindex');
    }

    public function updateEstado(Request $request, Cita $cita): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'estado' => ['required', 'string', 'in:aprobado,pospuesto,cancelado'],
        ])->validate();

        $cita->update($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó el estado de la cita #{$cita->id} a '{$validated['estado']}'.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Estado de la cita actualizado.')]);

        return to_route('Citasindex');
    }

    public function destroy(Cita $cita): RedirectResponse
    {
        $citaId = $cita->id;

        $cita->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó la cita #{$citaId}.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Cita eliminada exitosamente.')]);

        return to_route('Citasindex');
    }
}