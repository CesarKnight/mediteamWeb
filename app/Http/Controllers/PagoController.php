<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Paciente;
use App\Models\Servicio;
use App\Services\PagoFacilService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class PagoController extends Controller
{
    public function __construct(private PagoFacilService $pagoFacil) {}
    
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
            'pagoQrs' => $p->pagoQrs->map(fn($q) => [
                'id'         => $q->id,
                'estado'     => $q->estado,
                'expiracion' => $q->expiracion,
                'qr_base64'  => $q->qr_base64,
            ]),
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
            'estado'      => ['required', 'string', 'in:pagado,pendiente,anulado'],
            'metodo'      => ['required', 'string', 'in:efectivo,QR'],
        ])->validate();

        $pago = Pago::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Pago registrado exitosamente.')]);

        return to_route('Pagosshow', $pago);
    }

    private function syncPendingPagoQrs(Pago $pago): void
    {
        $pendientes = $pago->pagoQrs()->where('estado', 'pendiente')->get();

        foreach ($pendientes as $pagoQr) {
            if (!$pagoQr->pago_facil_id) {
                continue;
            }

            try {
                $values = $this->pagoFacil->queryTransaction((int) $pagoQr->pago_facil_id);
                $paymentStatus = $values['paymentStatus'] ?? null;

                if ($paymentStatus === 2) {
                    $pagoQr->update(['estado' => 'exitoso']);
                    $pago->update(['estado' => 'pagado']);
                } elseif ($paymentStatus === 4) {
                    $pagoQr->update(['estado' => 'anulado']);
                    $pago->update(['estado' => 'anulado']);
                }
                // paymentStatus === 1 ("En Proceso") -> still pending, no change
            } catch (\Throwable $e) {
                Log::error('PagoFacil query-transaction error', [
                    'pago_qr_id' => $pagoQr->id,
                    'error'      => $e->getMessage(),
                ]);
                // Swallow the error — a failed status check shouldn't block
                // the page from rendering with whatever data we already have.
            }
        }
    }

    public function show(Pago $pago): Response
    {
        $pago->load(['paciente.user', 'servicio', 'pagoQrs']);

        if ($pago->metodo === 'QR') {
            $this->syncPendingPagoQrs($pago);
            $pago->refresh()->load('pagoQrs');
        }

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
