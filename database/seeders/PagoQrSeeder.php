<?php

namespace Database\Seeders;

use App\Models\Pago;
use App\Models\PagoQr;
use Database\Seeders\Concerns\ConFechaHistorica;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PagoQrSeeder extends Seeder
{
    use ConFechaHistorica;

    /**
     * A valid 1x1 transparent PNG placeholder — the QR content itself
     * doesn't matter for seeded data, just that the field is populated.
     */
    private const QR_PLACEHOLDER = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

    public function run(): void
    {
        Pago::where('metodo', 'QR')
            ->whereDoesntHave('pagoQrs')
            ->get()
            ->each(function (Pago $pago) {
                $estado = match ($pago->estado) {
                    'pagado'  => 'exitoso',
                    'anulado' => 'anulado',
                    default   => 'pendiente',
                };

                $pagoQr = PagoQr::create([
                    'pago_id'            => $pago->id,
                    'transferencia_uuid' => (string) Str::uuid(),
                    'monto_recibido'     => $estado === 'exitoso' ? $pago->total : 0,
                    'pago_facil_id'      => $estado !== 'pendiente' ? strtoupper(Str::random(10)) : null,
                    'estado'             => $estado,
                    'qr_base64'          => self::QR_PLACEHOLDER,
                    'expiracion'         => (clone $pago->created_at)->addMinutes(30),
                ]);

                $this->conFecha($pagoQr, $pago->created_at);
            });
    }
}
