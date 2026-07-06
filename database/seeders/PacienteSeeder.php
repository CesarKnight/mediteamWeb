<?php

namespace Database\Seeders;

use App\Enums\PacienteEstado;
use App\Enums\UsuarioTipo;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    private const NOMBRES = [
        'María', 'José', 'Ana', 'Luis', 'Carmen', 'Pedro', 'Rosa', 'Juan', 'Elena', 'Diego',
        'Sofía', 'Mario', 'Valeria', 'Fernando', 'Gabriela', 'Ricardo', 'Daniela', 'Hugo', 'Paola', 'Iván',
        'Camila', 'Sergio', 'Natalia', 'Óscar', 'Verónica',
    ];

    private const APELLIDOS = [
        'Gutiérrez', 'Mendoza', 'Flores', 'Torrez', 'Rivera', 'Colque', 'Aramayo',
        'Paredes', 'Salazar', 'Cuellar', 'Guzmán', 'Camacho', 'Rodríguez', 'Escobar', 'Zabala',
    ];

    public function run(): void
    {
        foreach (self::NOMBRES as $i => $nombre) {
            $apellido = self::APELLIDOS[$i % count(self::APELLIDOS)];
            $email = 'paciente' . ($i + 1) . '@mediteam.test';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'            => $nombre,
                    'lastName'        => $apellido,
                    'ci'              => (string) (20000001 + $i),
                    'fechaNacimiento' => fake()->dateTimeBetween('-75 years', '-5 years')->format('Y-m-d'),
                    'telefono'        => (string) (70000000 + $i),
                    'tipo'            => UsuarioTipo::Paciente,
                    'password'        => bcrypt('password'),
                ],
            );

            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->saveQuietly();
            }

            Paciente::firstOrCreate(
                ['user_id' => $user->id],
                ['estado' => $i % 5 === 0 ? PacienteEstado::Baja : PacienteEstado::Alta],
            );
        }
    }
}
