<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Paciente;
use App\Models\Servicio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class PagoController extends Controller
{
    private function formatPago(Pago $p): array
    {
        return [
            'id'         => $p->id,
            'total'      => $p->total,
            'estado'     => $p->estado,
            'metodo'     => $p->metodo,
            'created_at' => $p->created_at,
            'paciente' => [
                'id'       => $p->paciente->id,
                'name'     => $p->paciente->user->name,
                'lastName' => $p->paciente->user->lastName,
            ],
            'servicio' => [
                'id'     => $p->servicio->id,
                'titulo' => $p->servicio->titulo,
                'precio' => $p->servicio->precio,
            ],
        ];
    }

    public function index(): Response
    {
        $pagos = Pago::with(['paciente.user', 'servicio'])
            ->latest()
            ->get()
            ->map(fn($p) => $this->formatPago($p));

        return Inertia::render('pagos/index', [
            'pagos' => $pagos,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('pagos/create', [
            'pacientes' => Paciente::with('user')->get()->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->user->name,
                'lastName' => $p->user->lastName,
            ]),
            'servicios' => Servicio::where('estado', 'disponible')->get()->map(fn($s) => [
                'id'     => $s->id,
                'titulo' => $s->titulo,
                'precio' => $s->precio,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'servicio_id' => ['required', 'exists:servicios,id'],
            'total'       => ['required', 'numeric', 'min:0'],
            'estado'      => ['required', 'string', 'in:pagado,pendiente'],
            'metodo'      => ['required', 'string', 'in:efectivo,QR'],
        ])->validate();

        $pago = Pago::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Pago registrado exitosamente.')]);

        return to_route('Pagosshow', $pago);
    }

    public function show(Pago $pago): Response
    {
        $pago->load(['paciente.user', 'servicio']);

        return Inertia::render('pagos/show', [
            'pago' => $this->formatPago($pago),
        ]);
    }

    public function destroy(Pago $pago): RedirectResponse
    {
        $pago->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Pago eliminado exitosamente.')]);

        return to_route('Pagosindex');
    }
}