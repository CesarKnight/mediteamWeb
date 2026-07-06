<?php

namespace Database\Seeders;

use App\Enums\PermisoEnum;
use App\Enums\UsuarioTipo;
use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    /**
     * Default permission set granted to each role. Keyed by UsuarioTipo::value.
     *
     * This is a starting point, not a final policy — adjust as the app's
     * authorization requirements are refined.
     *
     * @return array<string, list<PermisoEnum>>
     */
    private function defaultPermisos(): array
    {
        return [
            'administrador' => PermisoEnum::cases(),

            'secretaria' => [
                PermisoEnum::DashboardVer,
                PermisoEnum::PacienteVer,
                PermisoEnum::PacienteEditar,
                PermisoEnum::CitaVer,
                PermisoEnum::CitaCrear,
                PermisoEnum::CitaEditar,
                PermisoEnum::CitaEliminar,
                PermisoEnum::PagoVer,
                PermisoEnum::PagoCrear,
                PermisoEnum::PagoGenerarQr,
                PermisoEnum::ServicioVer,
                PermisoEnum::ReporteVer,
            ],

            'medico' => [
                PermisoEnum::DashboardVer,
                PermisoEnum::PacienteVer,
                PermisoEnum::PacienteEditar,
                PermisoEnum::HistoriaVer,
                PermisoEnum::HistoriaCrear,
                PermisoEnum::HistoriaEditar,
                PermisoEnum::CitaVer,
                PermisoEnum::CitaEditar,
                PermisoEnum::MedicoVer,
                PermisoEnum::ReporteVer,
            ],

            'paciente' => [
                PermisoEnum::DashboardVer,
                PermisoEnum::PagoVer,
                PermisoEnum::PagoGenerarQr,
                PermisoEnum::CitaVer,
            ],
        ];
    }

    /**
     * Sync the roles table with every case in App\Enums\UsuarioTipo, and
     * attach each role's default permission set from App\Enums\PermisoEnum.
     */
    public function run(): void
    {
        $defaults = $this->defaultPermisos();

        foreach (UsuarioTipo::cases() as $tipo) {
            $rol = Rol::firstOrCreate(['nombre' => $tipo->value]);

            $permisos = $defaults[$tipo->value] ?? [];

            $permisoIds = Permiso::whereIn(
                'nombre',
                array_map(fn (PermisoEnum $p) => $p->value, $permisos),
            )->pluck('id');

            $rol->permisos()->sync($permisoIds);
        }
    }
}
