<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ServicioController extends Controller
{
    public function __construct(private readonly BitacoraService $bitacora) {}

    private function formatServicio(Servicio $s): array
    {
        return [
            'id'          => $s->id,
            'titulo'      => $s->titulo,
            'descripcion' => $s->descripcion,
            'precio'      => $s->precio,
            'duracion'    => $s->duracion,
            'estado'      => $s->estado,
        ];
    }

    public function index(): Response
    {
        $servicios = Servicio::all()->map(fn($s) => $this->formatServicio($s));

        return Inertia::render('servicios/index', [
            'servicios' => $servicios,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('servicios/create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'titulo'      => ['required', 'string'],
            'descripcion' => ['required', 'string'],
            'precio'      => ['required', 'numeric'],
            'duracion'    => ['required', 'string'],
            'estado'      => ['required', 'string', 'in:disponible,no'],
        ])->validate();

        $servicio = Servicio::create($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " creó el servicio '{$servicio->titulo}' (id {$servicio->id}).",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Servicio creado exitosamente.')]);

        return to_route('Serviciosindex');
    }

    public function edit(Servicio $servicio): Response
    {
        return Inertia::render('servicios/edit', [
            'servicio' => $this->formatServicio($servicio),
        ]);
    }

    public function update(Request $request, Servicio $servicio): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'titulo'      => ['required', 'string'],
            'descripcion' => ['required', 'string'],
            'precio'      => ['required', 'numeric'],
            'duracion'    => ['required', 'string'],
            'estado'      => ['required', 'string', 'in:disponible,no'],
        ])->validate();

        $servicio->update($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó el servicio '{$servicio->titulo}' (id {$servicio->id}).",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Servicio actualizado exitosamente.')]);

        return to_route('Serviciosindex');
    }

    public function destroy(Servicio $servicio): RedirectResponse
    {
        $titulo = $servicio->titulo;
        $servicioId = $servicio->id;

        $servicio->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó el servicio '{$titulo}' (id {$servicioId}).",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Servicio eliminado exitosamente.')]);

        return to_route('Serviciosindex');
    }
}
