<?php

namespace Database\Seeders;

use App\Enums\UsuarioTipo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles/permisos must exist before any user is created, since
        // User's tipo -> rol_id sync hook (see User::booted()) looks up
        // the matching roles row when each user is saved.
        $this->call(PermisoSeeder::class);
        $this->call(RolSeeder::class);

        if (! User::where('email', 'admin@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'lastName' => 'Admin',
                'ci' => '12345678',
                'fechaNacimiento' => '1990-01-01',
                'telefono' => '1234567890',
                'tipo' => UsuarioTipo::Administrador,
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $this->call([
            ServicioSeeder::class,
            MedicoSeeder::class,
            PacienteSeeder::class,
            SecretariaSeeder::class,
            HistoriaSeeder::class,
            ConsultaSeeder::class,
            DiagnosticoSeeder::class,
            TratamientoSeeder::class,
            CitaSeeder::class,
            PagoSeeder::class,
            PagoQrSeeder::class,
            VisitaSeeder::class,
        ]);
    }
}
