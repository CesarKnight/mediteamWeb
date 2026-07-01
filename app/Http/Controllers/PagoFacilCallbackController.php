<?php

namespace App\Http\Controllers;

use App\Models\PagoQr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PagoFacilCallbackController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'PedidoID'   => ['required', 'string'],
            'Fecha'      => ['nullable', 'string'],
            'Hora'       => ['nullable', 'string'],
            'MetodoPago' => ['nullable', 'string'],
            'Estado'     => ['required', 'string'],
        ]);

        $pagoQr = PagoQr::where('transferencia_uuid', $validated['PedidoID'])->first();

        if (!$pagoQr) {
            Log::warning('PagoFacil callback: transferencia_uuid desconocido', $validated);
            // Acknowledge with 200 regardless, so PagoFácil doesn't retry indefinitely
            // on an unrecoverable mismatch.
            return response()->json(['received' => true]);
        }

        // Idempotency guard — a retried callback shouldn't re-process a finished transaction.
        if ($pagoQr->estado !== 'pendiente') {
            return response()->json(['received' => true]);
        }

        if ($validated['Estado'] === '2') {
            $pagoQr->update(['estado' => 'exitoso']);
            $pagoQr->pago->update(['estado' => 'pagado']);
        }

        // Other "Estado" codes (declines, expirations, etc.) aren't documented yet.
        // TODO: once PagoFácil specifies additional codes, map them to
        // $pagoQr->estado = 'anulado' + $pagoQr->pago->estado = 'anulado' here.

        return response()->json(['received' => true]);
    }
}