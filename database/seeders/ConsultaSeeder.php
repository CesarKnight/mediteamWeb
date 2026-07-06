<?php

namespace Database\Seeders;

use App\Models\Consulta;
use App\Models\Historia;
use Database\Seeders\Concerns\ConFechaHistorica;
use Illuminate\Database\Seeder;

class ConsultaSeeder extends Seeder
{
    use ConFechaHistorica;

    private const MOTIVOS = [
        'Dolor de cabeza persistente', 'Fiebre y malestar general', 'Dolor abdominal',
        'Revisión rutinaria', 'Dificultad para respirar', 'Dolor de espalda',
        'Control de presión arterial', 'Síntomas gripales', 'Dolor articular', 'Chequeo general',
    ];

    public function run(): void
    {
        Historia::all()->each(function (Historia $historia) {
            $cantidad = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $cantidad; $i++) {
                $fecha = fake()->dateTimeBetween($historia->created_at, 'now');

                $consulta = Consulta::create([
                    'historia_id' => $historia->id,
                    'motivo'      => fake()->randomElement(self::MOTIVOS),
                    'peso'        => fake()->randomFloat(1, 40, 110),
                    'altura'      => fake()->randomFloat(2, 1.40, 1.95),
                ]);

                $this->conFecha($consulta, $fecha);
            }
        });
    }
}
