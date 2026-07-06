<?php

namespace Database\Seeders;

use App\Enums\UsuarioTipo;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Roles/permisos must exist before any user is created, since the
        // admin user below needs a roles row to point rol_id at. This
        // seeder disables model events, so User's tipo -> rol_id sync hook
        // doesn't run here; rol_id is set explicitly instead.
        $this->call(PermisoSeeder::class);
        $this->call(RolSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'lastName' => 'Admin',
            'ci' => '12345678',
            'fechaNacimiento' => '1990-01-01',
            'telefono' => '1234567890',
            'tipo' => UsuarioTipo::Administrador,
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->forceFill([
            'rol_id' => Rol::where('nombre', UsuarioTipo::Administrador->value)->value('id'),
        ])->saveQuietly();
    }
}
