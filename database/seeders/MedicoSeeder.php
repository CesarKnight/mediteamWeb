<?php

namespace Database\Seeders;

use App\Enums\UsuarioTipo;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Database\Seeder;

class MedicoSeeder extends Seeder
{
    private const MEDICOS = [
        ['name' => 'Carlos', 'lastName' => 'Fernández', 'especialidad' => 'Medicina general'],
        ['name' => 'Lucía', 'lastName' => 'Rojas', 'especialidad' => 'Pediatría'],
        ['name' => 'Miguel', 'lastName' => 'Vargas', 'especialidad' => 'Ginecología'],
        ['name' => 'Andrea', 'lastName' => 'Quispe', 'especialidad' => 'Dermatología'],
        ['name' => 'Jorge', 'lastName' => 'Mamani', 'especialidad' => 'Traumatología'],
        ['name' => 'Patricia', 'lastName' => 'Choque', 'especialidad' => 'Cardiología'],
    ];

    public function run(): void
    {
        foreach (self::MEDICOS as $i => $datos) {
            $email = 'medico' . ($i + 1) . '@mediteam.test';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'            => $datos['name'],
                    'lastName'        => $datos['lastName'],
                    'ci'              => (string) (10000001 + $i),
                    'fechaNacimiento' => fake()->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d'),
                    'telefono'        => (string) (76000000 + $i),
                    'tipo'            => UsuarioTipo::Medico,
                    'password'        => bcrypt('password'),
                ],
            );

            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->saveQuietly();
            }

            Medico::firstOrCreate(
                ['user_id' => $user->id],
                ['especialidad' => $datos['especialidad']],
            );
        }
    }
}
