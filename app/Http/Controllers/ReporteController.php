<?php

namespace App\Http\Controllers;

use App\Models\Historia;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ReporteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('reportes/index');
    }

    private function pacientesParaSelector(): Collection
    {
        return Paciente::with('user')->get()->map(fn (Paciente $p) => [
            'id'       => $p->id,
            'name'     => $p->user->name,
            'lastName' => $p->user->lastName,
            'ci'       => $p->user->ci,
        ]);
    }

    public function historialPaciente(): Response
    {
        return Inertia::render('reportes/historial-paciente', [
            'pacientes' => $this->pacientesParaSelector(),
        ]);
    }

    private function eventosDeHistoria(Historia $historia): Collection
    {
        $consultas = $historia->consultas->map(fn ($c) => [
            'tipo'       => 'consulta',
            'fecha'      => $c->created_at,
            'motivo'     => $c->motivo,
            'peso'       => $c->peso,
            'altura'     => $c->altura,
        ]);

        $diagnosticos = $historia->diagnosticos->map(fn ($d) => [
            'tipo'        => 'diagnostico',
            'fecha'       => $d->created_at,
            'diagnostico' => $d->diagnostico,
            'enfermedad'  => $d->enfermedad,
            'gravedad'    => $d->gravedad,
        ]);

        $tratamientos = $historia->tratamientos->map(fn ($t) => [
            'tipo'             => 'tratamiento',
            'fecha'            => $t->created_at,
            'medicamento'      => $t->medicamento,
            'frecuencia_horas' => $t->frecuencia_horas,
        ]);

        return $consultas
            ->concat($diagnosticos)
            ->concat($tratamientos)
            ->sortBy('fecha')
            ->values();
    }

    public function generarHistorialPaciente(Request $request): Response
    {
        $validated = Validator::make($request->all(), [
            'paciente_id'  => ['required', 'exists:pacientes,id'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ])->validate();

        $paciente = Paciente::with('user')->findOrFail($validated['paciente_id']);

        $historias = Historia::where('paciente_id', $paciente->id)
            ->when($validated['fecha_inicio'] ?? null, fn ($q, $fecha) => $q->whereDate('created_at', '>=', $fecha))
            ->when($validated['fecha_fin'] ?? null, fn ($q, $fecha) => $q->whereDate('created_at', '<=', $fecha))
            ->with(['medicoCreador.user', 'medicosInvolucrados.user', 'consultas', 'diagnosticos', 'tratamientos'])
            ->orderBy('created_at')
            ->get()
            ->map(fn (Historia $h) => [
                'id'                  => $h->id,
                'estado'              => $h->estado,
                'created_at'          => $h->created_at,
                'medicoCreador'       => [
                    'name'         => $h->medicoCreador->user->name,
                    'lastName'     => $h->medicoCreador->user->lastName,
                    'especialidad' => $h->medicoCreador->especialidad,
                ],
                'medicosInvolucrados' => $h->medicosInvolucrados->map(fn ($m) => [
                    'name'     => $m->user->name,
                    'lastName' => $m->user->lastName,
                ]),
                'eventos' => $this->eventosDeHistoria($h),
            ]);

        $data = [
            'paciente'     => [
                'name'            => $paciente->user->name,
                'lastName'        => $paciente->user->lastName,
                'ci'              => $paciente->user->ci,
                'fechaNacimiento' => $paciente->user->fechaNacimiento,
                'telefono'        => $paciente->user->telefono,
                'estado'          => $paciente->estado,
            ],
            'historias'    => $historias,
            'fechaInicio'  => $validated['fecha_inicio'] ?? null,
            'fechaFin'     => $validated['fecha_fin'] ?? null,
            'generadoEn'   => now(),
        ];

        $pdf = Pdf::loadView('reportes.historial-paciente-pdf', $data)->setPaper('letter');

        return Inertia::render('reportes/historial-paciente', [
            'pacientes' => $this->pacientesParaSelector(),
            'filtros'   => [
                'paciente_id'  => $paciente->id,
                'fecha_inicio' => $validated['fecha_inicio'] ?? null,
                'fecha_fin'    => $validated['fecha_fin'] ?? null,
            ],
            'pdfBase64' => base64_encode($pdf->output()),
            'resumen'   => [
                'paciente'          => trim($paciente->user->name.' '.$paciente->user->lastName),
                'totalHistorias'    => $historias->count(),
                'totalConsultas'    => $historias->sum(fn ($h) => $h['eventos']->where('tipo', 'consulta')->count()),
                'totalDiagnosticos' => $historias->sum(fn ($h) => $h['eventos']->where('tipo', 'diagnostico')->count()),
                'totalTratamientos' => $historias->sum(fn ($h) => $h['eventos']->where('tipo', 'tratamiento')->count()),
            ],
        ]);
    }

    public function historialTratamientos(): Response
    {
        return Inertia::render('reportes/historial-tratamientos', [
            'pacientes' => $this->pacientesParaSelector(),
        ]);
    }

    public function generarHistorialTratamientos(Request $request): Response
    {
        $validated = Validator::make($request->all(), [
            'paciente_id'  => ['required', 'exists:pacientes,id'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ])->validate();

        $paciente = Paciente::with('user')->findOrFail($validated['paciente_id']);

        $tratamientos = Tratamiento::whereHas('historia', fn ($q) => $q->where('paciente_id', $paciente->id))
            ->when($validated['fecha_inicio'] ?? null, fn ($q, $fecha) => $q->whereDate('created_at', '>=', $fecha))
            ->when($validated['fecha_fin'] ?? null, fn ($q, $fecha) => $q->whereDate('created_at', '<=', $fecha))
            ->with('historia.medicoCreador.user')
            ->orderBy('created_at')
            ->get()
            ->map(fn (Tratamiento $t) => [
                'id'               => $t->id,
                'medicamento'      => $t->medicamento,
                'frecuencia_horas' => $t->frecuencia_horas,
                'created_at'       => $t->created_at,
                'historia'         => [
                    'id'     => $t->historia->id,
                    'estado' => $t->historia->estado,
                ],
                'medico' => [
                    'name'         => $t->historia->medicoCreador->user->name,
                    'lastName'     => $t->historia->medicoCreador->user->lastName,
                    'especialidad' => $t->historia->medicoCreador->especialidad,
                ],
            ]);

        $data = [
            'paciente'    => [
                'name'            => $paciente->user->name,
                'lastName'        => $paciente->user->lastName,
                'ci'              => $paciente->user->ci,
                'fechaNacimiento' => $paciente->user->fechaNacimiento,
                'telefono'        => $paciente->user->telefono,
                'estado'          => $paciente->estado,
            ],
            'tratamientos' => $tratamientos,
            'fechaInicio'  => $validated['fecha_inicio'] ?? null,
            'fechaFin'     => $validated['fecha_fin'] ?? null,
            'generadoEn'   => now(),
        ];

        $pdf = Pdf::loadView('reportes.historial-tratamientos-pdf', $data)->setPaper('letter');

        return Inertia::render('reportes/historial-tratamientos', [
            'pacientes' => $this->pacientesParaSelector(),
            'filtros'   => [
                'paciente_id'  => $paciente->id,
                'fecha_inicio' => $validated['fecha_inicio'] ?? null,
                'fecha_fin'    => $validated['fecha_fin'] ?? null,
            ],
            'pdfBase64' => base64_encode($pdf->output()),
            'resumen'   => [
                'paciente'           => trim($paciente->user->name.' '.$paciente->user->lastName),
                'totalTratamientos'  => $tratamientos->count(),
                'medicamentosUnicos' => $tratamientos->pluck('medicamento')->unique()->count(),
            ],
        ]);
    }

    private function medicosParaSelector(): Collection
    {
        return Medico::with('user')->get()->map(fn (Medico $m) => [
            'id'           => $m->id,
            'name'         => $m->user->name,
            'lastName'     => $m->user->lastName,
            'especialidad' => $m->especialidad,
        ]);
    }

    public function historialMedico(): Response
    {
        return Inertia::render('reportes/historial-medico', [
            'medicos' => $this->medicosParaSelector(),
        ]);
    }

    public function generarHistorialMedico(Request $request): Response
    {
        $validated = Validator::make($request->all(), [
            'medico_id'    => ['required', 'exists:medicos,id'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ])->validate();

        $medico = Medico::with('user')->findOrFail($validated['medico_id']);

        $historias = $medico->historiasInvolucradas()
            ->when(
                $validated['fecha_inicio'] ?? null,
                fn ($q, $fecha) => $q->where('historia_medico.created_at', '>=', $fecha.' 00:00:00'),
            )
            ->when(
                $validated['fecha_fin'] ?? null,
                fn ($q, $fecha) => $q->where('historia_medico.created_at', '<=', $fecha.' 23:59:59'),
            )
            ->with(['paciente.user', 'medicoCreador.user', 'consultas', 'diagnosticos', 'tratamientos'])
            ->orderBy('historia_medico.created_at')
            ->get()
            ->map(fn (Historia $h) => [
                'id'                    => $h->id,
                'estado'                => $h->estado,
                'fechaInvolucramiento'  => $h->pivot->created_at,
                'paciente'              => [
                    'name'     => $h->paciente->user->name,
                    'lastName' => $h->paciente->user->lastName,
                    'ci'       => $h->paciente->user->ci,
                ],
                'medicoCreador'         => [
                    'name'     => $h->medicoCreador->user->name,
                    'lastName' => $h->medicoCreador->user->lastName,
                ],
                'totalConsultas'        => $h->consultas->count(),
                'totalDiagnosticos'     => $h->diagnosticos->count(),
                'totalTratamientos'     => $h->tratamientos->count(),
            ]);

        $data = [
            'medico'      => [
                'name'         => $medico->user->name,
                'lastName'     => $medico->user->lastName,
                'ci'           => $medico->user->ci,
                'telefono'     => $medico->user->telefono,
                'especialidad' => $medico->especialidad,
            ],
            'historias'   => $historias,
            'fechaInicio' => $validated['fecha_inicio'] ?? null,
            'fechaFin'    => $validated['fecha_fin'] ?? null,
            'generadoEn'  => now(),
        ];

        $pdf = Pdf::loadView('reportes.historial-medico-pdf', $data)->setPaper('letter');

        return Inertia::render('reportes/historial-medico', [
            'medicos'   => $this->medicosParaSelector(),
            'filtros'   => [
                'medico_id'    => $medico->id,
                'fecha_inicio' => $validated['fecha_inicio'] ?? null,
                'fecha_fin'    => $validated['fecha_fin'] ?? null,
            ],
            'pdfBase64' => base64_encode($pdf->output()),
            'resumen'   => [
                'medico'          => trim($medico->user->name.' '.$medico->user->lastName),
                'totalHistorias'  => $historias->count(),
                'pacientesUnicos' => $historias->pluck('paciente.ci')->unique()->count(),
                'totalConsultas'  => $historias->sum('totalConsultas'),
            ],
        ]);
    }

    public function historialCitas(): Response
    {
        return Inertia::render('reportes/historial-citas');
    }

    public function historialPagosPaciente(): Response
    {
        return Inertia::render('reportes/historial-pagos-paciente');
    }
}
