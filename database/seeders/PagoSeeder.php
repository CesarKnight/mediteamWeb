<?php

namespace Database\Seeders;

use App\Models\Pago;
use App\Models\Paciente;
use App\Models\Servicio;
use Database\Seeders\Concerns\ConFechaHistorica;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class PagoSeeder extends Seeder
{
    use ConFechaHistorica;

    private const CANTIDAD_HISTORICA = 90;
    private const CANTIDAD_RECIENTE  = 40;

    public function run(): void
    {
        $pacienteIds = Paciente::pluck('id')->all();
        $servicios   = Servicio::all();

        if (empty($pacienteIds) || $servicios->isEmpty()) {
            return;
        }

        for ($i = 0; $i < self::CANTIDAD_HISTORICA; $i++) {
            $this->crearPago($pacienteIds, $servicios, fake()->dateTimeBetween('2025-01-01', 'now'));
        }

        // Bias extra density into the last 6 months so the dashboard's
        // ingresos-por-mes chart always has recent bars, regardless of
        // exactly when this seeder is run.
        for ($i = 0; $i < self::CANTIDAD_RECIENTE; $i++) {
            $this->crearPago($pacienteIds, $servicios, fake()->dateTimeBetween('-6 months', 'now'));
        }
    }

    /** @param  list<int>  $pacienteIds */
    private function crearPago(array $pacienteIds, Collection $servicios, \DateTimeInterface $fecha): void
    {
        $servicio  = fake()->randomElement($servicios->all());
        $variacion = fake()->randomFloat(2, 0.9, 1.1);

        $pago = Pago::create([
            'paciente_id' => fake()->randomElement($pacienteIds),
            'servicio_id' => $servicio->id,
            'total'       => round($servicio->precio * $variacion, 2),
            'estado'      => $this->estadoAleatorio(),
            'metodo'      => fake()->randomElement(['efectivo', 'efectivo', 'efectivo', 'QR', 'QR']),
        ]);

        $this->conFecha($pago, $fecha);
    }

    private function estadoAleatorio(): string
    {
        return fake()->randomElement([
            'pagado', 'pagado', 'pagado', 'pagado', 'pagado', 'pagado', 'pagado',
            'pendiente', 'pendiente',
            'anulado',
        ]);
    }
}
