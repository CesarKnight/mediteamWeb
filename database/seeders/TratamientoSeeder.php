<?php

namespace Database\Seeders;

use App\Models\Historia;
use App\Models\Tratamiento;
use Database\Seeders\Concerns\ConFechaHistorica;
use Illuminate\Database\Seeder;

class TratamientoSeeder extends Seeder
{
    use ConFechaHistorica;

    private const MEDICAMENTOS = [
        'Paracetamol 500mg', 'Ibuprofeno 400mg', 'Amoxicilina 500mg', 'Omeprazol 20mg',
        'Loratadina 10mg', 'Losartán 50mg', 'Metformina 850mg', 'Diclofenaco 50mg',
        'Salbutamol inhalador', 'Complejo B',
    ];

    private const FRECUENCIAS = [6, 8, 12, 24];

    public function run(): void
    {
        Historia::all()->each(function (Historia $historia) {
            if (! fake()->boolean(60)) {
                return;
            }

            $cantidad = fake()->numberBetween(1, 2);

            for ($i = 0; $i < $cantidad; $i++) {
                $fecha = fake()->dateTimeBetween($historia->created_at, 'now');

                $tratamiento = Tratamiento::create([
                    'historia_id'      => $historia->id,
                    'medicamento'      => fake()->randomElement(self::MEDICAMENTOS),
                    'frecuencia_horas' => fake()->randomElement(self::FRECUENCIAS),
                ]);

                $this->conFecha($tratamiento, $fecha);
            }
        });
    }
}
