<?php

namespace Database\Seeders;

use App\Enums\HistoriaEstado;
use App\Models\Historia;
use App\Models\Medico;
use App\Models\Paciente;
use Database\Seeders\Concerns\ConFechaHistorica;
use Illuminate\Database\Seeder;

class HistoriaSeeder extends Seeder
{
    use ConFechaHistorica;

    private const CANTIDAD = 24;

    private const PESOS_ESTADO = [
        HistoriaEstado::Finalizado->value => 55,
        HistoriaEstado::Pendiente->value  => 30,
        HistoriaEstado::Anulado->value    => 15,
    ];

    public function run(): void
    {
        $pacienteIds = Paciente::pluck('id')->all();
        $medicoIds   = Medico::pluck('id')->all();

        if (empty($pacienteIds) || empty($medicoIds)) {
            return;
        }

        for ($i = 0; $i < self::CANTIDAD; $i++) {
            $pacienteId = fake()->randomElement($pacienteIds);
            $medicoId   = fake()->randomElement($medicoIds);
            $estado     = $this->estadoAleatorio();
            $creadaEn   = fake()->dateTimeBetween('2025-01-01', 'now');

            $historia = Historia::create([
                'estado'      => $estado,
                'paciente_id' => $pacienteId,
                'medico_id'   => $medicoId,
            ]);

            $this->conFecha($historia, $creadaEn);

            $otrosMedicos = array_values(array_diff($medicoIds, [$medicoId]));

            if (! empty($otrosMedicos) && fake()->boolean(40)) {
                $involucrados = fake()->randomElements(
                    $otrosMedicos,
                    min(count($otrosMedicos), fake()->numberBetween(1, 2)),
                );

                foreach ($involucrados as $involucradoId) {
                    $fechaInvolucramiento = fake()->dateTimeBetween($creadaEn, 'now');

                    $historia->medicosInvolucrados()->attach($involucradoId, [
                        'created_at' => $fechaInvolucramiento,
                        'updated_at' => $fechaInvolucramiento,
                    ]);
                }
            }
        }
    }

    private function estadoAleatorio(): HistoriaEstado
    {
        $valor = fake()->randomElement(array_merge(
            ...array_map(
                fn ($estado, $peso) => array_fill(0, $peso, $estado),
                array_keys(self::PESOS_ESTADO),
                array_values(self::PESOS_ESTADO),
            ),
        ));

        return HistoriaEstado::from($valor);
    }
}
