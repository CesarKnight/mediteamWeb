<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Services\PagoFacilService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PagoQrController extends Controller
{
    public function __construct(private PagoFacilService $pagoFacil) {}

    public function store(Pago $pago): RedirectResponse
    {
        abort_if($pago->metodo !== 'QR', 422, 'Este pago no utiliza método QR.');
        abort_if($pago->estado === 'pagado', 422, 'Este pago ya fue confirmado.');

        // avoid generating a duplicate QR while one is still awaiting confirmation
        if ($pago->pagoQrs()->where('estado', 'pendiente')->exists()) {
            return to_route('Pagosshow', $pago);
        }

        try {
            $transferenciaUuid = (string) Str::uuid();
            $values = $this->pagoFacil->generateQr($pago, $transferenciaUuid);

            $pago->pagoQrs()->create([
                'transferencia_uuid' => $transferenciaUuid,
                'monto_recibido'     => $pago->total,
                'pago_facil_id'      => (string) ($values['transactionId'] ?? ''),
                'estado'             => 'pendiente',
                'qr_base64'          => $values['qrBase64'] ?? null,
                'expiracion'         => $values['expirationDate'] ?? null,
            ]);

            Inertia::flash('toast', ['type' => 'success', 'message' => __('Código QR generado exitosamente.')]);
        } catch (\Throwable $e) {
            Log::error('PagoFacil generate-qr error', ['pago_id' => $pago->id, 'error' => $e->getMessage()]);
            Inertia::flash('toast', ['type' => 'error', 'message' => __('No se pudo generar el código QR. Intenta nuevamente.')]);
        }

        return to_route('Pagosshow', $pago);
    }
    
}