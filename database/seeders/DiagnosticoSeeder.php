<?php

namespace Database\Seeders;

use App\Models\Diagnostico;
use App\Models\Historia;
use Database\Seeders\Concerns\ConFechaHistorica;
use Illuminate\Database\Seeder;

class DiagnosticoSeeder extends Seeder
{
    use ConFechaHistorica;

    private const ENFERMEDADES = [
        'Gripe común', 'Hipertensión arterial', 'Gastritis', 'Migraña', 'Diabetes tipo 2',
        'Faringitis', 'Lumbalgia', 'Alergia estacional', 'Bronquitis', 'Ansiedad',
    ];

    private const GRAVEDADES = ['leve', 'leve', 'leve', 'medio', 'medio', 'severo'];

    public function run(): void
    {
        Historia::all()->each(function (Historia $historia) {
            if (! fake()->boolean(70)) {
                return;
            }

            $cantidad = fake()->numberBetween(1, 2);

            for ($i = 0; $i < $cantidad; $i++) {
                $fecha = fake()->dateTimeBetween($historia->created_at, 'now');
                $enfermedad = fake()->randomElement(self::ENFERMEDADES);

                $diagnostico = Diagnostico::create([
                    'historia_id' => $historia->id,
                    'diagnostico' => "Diagnóstico compatible con {$enfermedad}, tratado según protocolo estándar.",
                    'enfermedad'  => $enfermedad,
                    'gravedad'    => fake()->randomElement(self::GRAVEDADES),
                ]);

                $this->conFecha($diagnostico, $fecha);
            }
        });
    }
}
