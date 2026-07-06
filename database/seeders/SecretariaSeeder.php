<?php

namespace Database\Seeders;

use App\Enums\UsuarioTipo;
use App\Models\Secretaria;
use App\Models\User;
use Illuminate\Database\Seeder;

class SecretariaSeeder extends Seeder
{
    private const SECRETARIAS = [
        ['name' => 'Fabiola', 'lastName' => 'Nina', 'profesion' => 'Administración en salud'],
        ['name' => 'Mauricio', 'lastName' => 'Apaza', 'profesion' => 'Secretariado ejecutivo'],
        ['name' => 'Silvia', 'lastName' => 'Condori', 'profesion' => 'Gestión administrativa'],
    ];

    public function run(): void
    {
        foreach (self::SECRETARIAS as $i => $datos) {
            $email = 'secretaria' . ($i + 1) . '@mediteam.test';

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'            => $datos['name'],
                    'lastName'        => $datos['lastName'],
                    'ci'              => (string) (30000001 + $i),
                    'fechaNacimiento' => fake()->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
                    'telefono'        => (string) (65000000 + $i),
                    'tipo'            => UsuarioTipo::Secretaria,
                    'password'        => bcrypt('password'),
                ],
            );

            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->saveQuietly();
            }

            Secretaria::firstOrCreate(
                ['user_id' => $user->id],
                ['profesion' => $datos['profesion']],
            );
        }
    }
}
