<?php

namespace App\Http\Controllers;

use App\Enums\PermisoEnum;
use App\Models\Cita;
use App\Models\Historia;
use App\Models\Medico;
use App\Models\Pago;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Ordered fallback for users without Dashboard.ver: mirrors the
     * sidebar's nav order, so a role that can't see the dashboard lands on
     * the first resource it's actually allowed to see instead of a 403.
     *
     * @var list<array{0: PermisoEnum, 1: string}>
     */
    private const RUTA_POR_PERMISO = [
        [PermisoEnum::UsuarioVer, 'Usuariosindex'],
        [PermisoEnum::PacienteVer, 'Pacientesindex'],
        [PermisoEnum::MedicoVer, 'Medicosindex'],
        [PermisoEnum::SecretariaVer, 'Secretariasindex'],
        [PermisoEnum::HistoriaVer, 'Historiasindex'],
        [PermisoEnum::ServicioVer, 'Serviciosindex'],
        [PermisoEnum::CitaVer, 'Citasindex'],
        [PermisoEnum::PagoVer, 'Pagosindex'],
        [PermisoEnum::ReporteVer, 'Reportesindex'],
        [PermisoEnum::BitacoraVer, 'Bitacoraindex'],
        [PermisoEnum::PermisoVer, 'Permisosindex'],
    ];

    public function index(Request $request): Response|RedirectResponse
    {
        /** @var User $usuario */
        $usuario = $request->user();

        if (! $usuario->tienePermiso(PermisoEnum::DashboardVer)) {
            return $this->redirigirAPrimeraRutaDisponible($usuario);
        }

        return Inertia::render('dashboard', [
            'kpis'               => $this->kpis(),
            'citasHoy'           => $this->citasHoy(),
            'proximasCitas'      => $this->proximasCitas(),
            'ingresosPorMes'     => $this->ingresosPorMes(),
            'citasPorEstado'     => $this->citasPorEstado(),
            'historiasPorEstado' => $this->historiasPorEstado(),
            'serviciosTop'       => $this->serviciosTop(),
            'pagosPendientes'    => $this->pagosPendientes(),
        ]);
    }

    private function redirigirAPrimeraRutaDisponible(User $usuario): Response|RedirectResponse
    {
        foreach (self::RUTA_POR_PERMISO as [$permiso, $ruta]) {
            if ($usuario->tienePermiso($permiso)) {
                return redirect()->route($ruta);
            }
        }

        return Inertia::render('sin-acceso');
    }

    private function kpis(): array
    {
        return [
            'citasHoy' => Cita::whereDate('hora_inicio', today())
                ->where('estado', '!=', 'cancelado')
                ->count(),
            'ingresosMes' => (float) Pago::where('estado', 'pagado')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total'),
            'pacientesActivos'    => Paciente::where('estado', 'alta')->count(),
            'historiasPendientes' => Historia::where('estado', 'pendiente')->count(),
            'pagosPendientes'     => Pago::where('estado', 'pendiente')->count(),
            'medicosTotal'        => Medico::count(),
        ];
    }

    private function citasHoy(): array
    {
        return Cita::with(['paciente.user', 'medico.user', 'servicio'])
            ->whereDate('hora_inicio', today())
            ->where('estado', '!=', 'cancelado')
            ->orderBy('hora_inicio')
            ->get()
            ->map(fn($c) => $this->formatCitaResumen($c))
            ->all();
    }

    private function proximasCitas(): array
    {
        return Cita::with(['paciente.user', 'medico.user', 'servicio'])
            ->where('hora_inicio', '>', now())
            ->where('estado', '!=', 'cancelado')
            ->orderBy('hora_inicio')
            ->take(5)
            ->get()
            ->map(fn($c) => $this->formatCitaResumen($c))
            ->all();
    }

    private function formatCitaResumen(Cita $c): array
    {
        return [
            'id'          => $c->id,
            'estado'      => $c->estado,
            'hora_inicio' => $c->hora_inicio,
            'paciente'    => "{$c->paciente->user->name} {$c->paciente->user->lastName}",
            'medico'      => "{$c->medico->user->name} {$c->medico->user->lastName}",
            'servicio'    => $c->servicio->titulo,
        ];
    }

    private function ingresosPorMes(): array
    {
        return collect(range(5, 0))->map(function ($mesesAtras) {
            $mes    = now()->subMonths($mesesAtras);
            $inicio = $mes->copy()->startOfMonth();
            $fin    = $mes->copy()->endOfMonth();

            $total = Pago::where('estado', 'pagado')
                ->whereBetween('created_at', [$inicio, $fin])
                ->sum('total');

            return [
                'mes'   => $mes->locale('es')->isoFormat('MMM'),
                'total' => (float) $total,
            ];
        })->values()->all();
    }

    private function citasPorEstado(): array
    {
        $counts = Cita::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return collect(['aprobado', 'pospuesto', 'cancelado'])
            ->map(fn($estado) => [
                'estado' => $estado,
                'total'  => (int) ($counts[$estado] ?? 0),
            ])->all();
    }

    private function historiasPorEstado(): array
    {
        $counts = Historia::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return collect(['pendiente', 'anulado', 'finalizado'])
            ->map(fn($estado) => [
                'estado' => $estado,
                'total'  => (int) ($counts[$estado] ?? 0),
            ])->all();
    }

    private function serviciosTop(): array
    {
        return Servicio::withSum(['pagos as ingresos' => function ($q) {
                $q->where('estado', 'pagado');
            }], 'total')
            ->orderByDesc('ingresos')
            ->take(5)
            ->get()
            ->map(fn($s) => [
                'id'       => $s->id,
                'titulo'   => $s->titulo,
                'ingresos' => (float) ($s->ingresos ?? 0),
            ])->all();
    }

    private function pagosPendientes(): array
    {
        return Pago::with(['paciente.user', 'servicio'])
            ->where('estado', 'pendiente')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id,
                'total'    => (float) $p->total,
                'metodo'   => $p->metodo,
                'paciente' => "{$p->paciente->user->name} {$p->paciente->user->lastName}",
                'servicio' => $p->servicio->titulo,
            ])->all();
    }
}