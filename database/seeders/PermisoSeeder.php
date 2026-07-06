<?php

namespace Database\Seeders;

use App\Enums\PermisoEnum;
use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /**
     * Sync the permisos table with every case declared in App\Enums\PermisoEnum.
     */
    public function run(): void
    {
        foreach (PermisoEnum::cases() as $permiso) {
            Permiso::firstOrCreate(['nombre' => $permiso->value]);
        }
    }
}
