<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Servicio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CitaSeeder extends Seeder
{
    // 08:00 to 17:30 in 30-minute increments — a fixed grid so two
    // appointments for the same médico can never overlap: each takes
    // exactly one slot.
    private const SLOTS = 20;
    private const DURACION_MINUTOS = 30;

    private const CANTIDAD_HISTORICAS = 160;
    private const CANTIDAD_HOY = 5;
    private const CANTIDAD_PROXIMAS = 12;

    /** @var array<string, array<int, true>> keyed by "{medicoId}|{Y-m-d}" => [slotIndex => true] */
    private array $ocupados = [];

    public function run(): void
    {
        $medicoIds   = Medico::pluck('id')->all();
        $pacienteIds = Paciente::pluck('id')->all();
        $servicioIds = Servicio::pluck('id')->all();

        if (empty($medicoIds) || empty($pacienteIds) || empty($servicioIds)) {
            return;
        }

        $hoy = Carbon::today();

        // Historical spread: 2025-01-01 through yesterday.
        for ($i = 0; $i < self::CANTIDAD_HISTORICAS; $i++) {
            $dia = Carbon::parse(fake()->dateTimeBetween('2025-01-01', $hoy->copy()->subDay()))->startOfDay();
            $this->crearCita($dia, $medicoIds, $pacienteIds, $servicioIds, permitirCancelado: true);
        }

        // A handful of appointments today, so the dashboard's "citas de hoy" widget has data.
        for ($i = 0; $i < self::CANTIDAD_HOY; $i++) {
            $this->crearCita($hoy->copy(), $medicoIds, $pacienteIds, $servicioIds, permitirCancelado: false);
        }

        // Upcoming appointments, for the "próximas citas" widget.
        for ($i = 0; $i < self::CANTIDAD_PROXIMAS; $i++) {
            $dia = $hoy->copy()->addDays(fake()->numberBetween(1, 21))->startOfDay();
            $this->crearCita($dia, $medicoIds, $pacienteIds, $servicioIds, permitirCancelado: false);
        }
    }

    /**
     * @param  list<int>  $medicoIds
     * @param  list<int>  $pacienteIds
     * @param  list<int>  $servicioIds
     */
    private function crearCita(Carbon $dia, array $medicoIds, array $pacienteIds, array $servicioIds, bool $permitirCancelado): void
    {
        $medicoId = fake()->randomElement($medicoIds);
        $clave    = $medicoId . '|' . $dia->toDateString();

        $slot = $this->slotDisponible($clave);

        if ($slot === null) {
            return; // ese médico ya no tiene cupos libres ese día, se omite
        }

        $this->ocupados[$clave][$slot] = true;

        $horaInicio = $dia->copy()->addMinutes($slot * self::DURACION_MINUTOS + 8 * 60);
        $horaFin    = $horaInicio->copy()->addMinutes(self::DURACION_MINUTOS);

        Cita::create([
            'paciente_id' => fake()->randomElement($pacienteIds),
            'medico_id'   => $medicoId,
            'servicio_id' => fake()->randomElement($servicioIds),
            'hora_inicio' => $horaInicio,
            'hora_fin'    => $horaFin,
            'estado'      => $this->estadoAleatorio($permitirCancelado),
        ]);
    }

    private function slotDisponible(string $clave): ?int
    {
        for ($intento = 0; $intento < 20; $intento++) {
            $slot = fake()->numberBetween(0, self::SLOTS - 1);

            if (! isset($this->ocupados[$clave][$slot])) {
                return $slot;
            }
        }

        return null;
    }

    private function estadoAleatorio(bool $permitirCancelado): string
    {
        if (! $permitirCancelado) {
            return fake()->randomElement(['aprobado', 'aprobado', 'aprobado', 'pospuesto']);
        }

        return fake()->randomElement([
            'aprobado', 'aprobado', 'aprobado', 'aprobado', 'aprobado', 'aprobado',
            'pospuesto', 'pospuesto',
            'cancelado', 'cancelado',
        ]);
    }
}
