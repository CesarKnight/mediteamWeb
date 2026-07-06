<?php

namespace App\Http\Controllers;

use App\Enums\PermisoEnum;
use App\Models\Permiso;
use App\Models\Rol;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PermisoController extends Controller
{
    public function __construct(private readonly BitacoraService $bitacora) {}

    /**
     * Canonical column order for the CRUD-style operations. Any operation
     * found in PermisoEnum that isn't listed here is appended after, in the
     * order it's first encountered — so new verbs added to the enum show up
     * automatically as extra columns.
     */
    private const ORDEN_OPERACIONES = ['ver', 'crear', 'editar', 'eliminar'];

    /**
     * Parses every PermisoEnum case ("Recurso.operacion") into the matrix
     * shape the page needs: an ordered list of operation column names, and
     * one row per resource mapping each operation to its permiso value (or
     * null when that resource doesn't have that operation).
     *
     * @return array{operaciones: list<string>, recursos: list<array{nombre: string, operaciones: array<string, string|null>}>}
     */
    private function construirMatriz(): array
    {
        $porRecurso = [];
        $operaciones = [];

        foreach (PermisoEnum::cases() as $permiso) {
            [$recurso, $operacion] = explode('.', $permiso->value, 2);

            $porRecurso[$recurso][$operacion] = $permiso->value;

            if (!in_array($operacion, $operaciones, true)) {
                $operaciones[] = $operacion;
            }
        }

        usort($operaciones, function ($a, $b) {
            $posA = array_search($a, self::ORDEN_OPERACIONES, true);
            $posB = array_search($b, self::ORDEN_OPERACIONES, true);

            return match (true) {
                $posA !== false && $posB !== false => $posA <=> $posB,
                $posA !== false => -1,
                $posB !== false => 1,
                default => 0,
            };
        });

        $recursos = [];
        foreach ($porRecurso as $nombre => $operacionesDelRecurso) {
            $recursos[] = [
                'nombre' => $nombre,
                'operaciones' => collect($operaciones)
                    ->mapWithKeys(fn ($op) => [$op => $operacionesDelRecurso[$op] ?? null])
                    ->all(),
            ];
        }

        return ['operaciones' => $operaciones, 'recursos' => $recursos];
    }

    public function index(): Response
    {
        $matriz = $this->construirMatriz();

        $roles = Rol::with('permisos')->orderBy('id')->get();

        $permisosPorRol = $roles->mapWithKeys(fn (Rol $rol) => [
            $rol->id => $rol->permisos->pluck('nombre'),
        ]);

        return Inertia::render('permisos/index', [
            'roles' => $roles->map(fn (Rol $rol) => [
                'id' => $rol->id,
                'nombre' => $rol->nombre,
            ]),
            'operaciones' => $matriz['operaciones'],
            'recursos' => $matriz['recursos'],
            'permisosPorRol' => $permisosPorRol,
        ]);
    }

    public function toggle(Request $request, Rol $rol): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'permiso' => ['required', 'string', Rule::enum(PermisoEnum::class)],
            'habilitado' => ['required', 'boolean'],
        ])->validate();

        $permiso = Permiso::where('nombre', $validated['permiso'])->firstOrFail();

        if ($validated['habilitado']) {
            $rol->permisos()->syncWithoutDetaching([$permiso->id]);
        } else {
            $rol->permisos()->detach($permiso->id);
        }

        $accion = $validated['habilitado'] ? 'habilitó' : 'deshabilitó';

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " {$accion} el permiso '{$permiso->nombre}' para el rol '{$rol->nombre}'.",
            static::class,
        );

        return back();
    }
}
