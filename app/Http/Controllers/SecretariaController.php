<?php

namespace App\Http\Controllers;

use App\Concerns\ProfileValidationRules;
use App\Models\Secretaria;
use App\Services\BitacoraService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class SecretariaController extends Controller
{
    use ProfileValidationRules;

    public function __construct(private readonly BitacoraService $bitacora) {}

    public function index(): Response
    {
        $secretarias = Secretaria::with('user')
            ->get()
            ->map(fn($s) => [
                'id'        => $s->id,
                'profesion' => $s->profesion,
                'user' => [
                    'id'              => $s->user->id,
                    'name'            => $s->user->name,
                    'lastName'        => $s->user->lastName,
                    'ci'              => $s->user->ci,
                    'fechaNacimiento' => $s->user->fechaNacimiento,
                    'telefono'        => $s->user->telefono,
                    'email'           => $s->user->email,
                ],
            ]);

        return Inertia::render('secretarias/index', [
            'secretarias' => $secretarias,
        ]);
    }

    public function show(Secretaria $secretaria): Response
    {
        $secretaria->load('user');

        return Inertia::render('secretarias/show', [
            'secretaria' => [
                'id'        => $secretaria->id,
                'profesion' => $secretaria->profesion,
            ],
            'user' => [
                'id'              => $secretaria->user->id,
                'name'            => $secretaria->user->name,
                'lastName'        => $secretaria->user->lastName,
                'ci'              => $secretaria->user->ci,
                'fechaNacimiento' => $secretaria->user->fechaNacimiento,
                'telefono'        => $secretaria->user->telefono,
                'email'           => $secretaria->user->email,
            ],
        ]);
    }

    public function edit(Secretaria $secretaria): Response
    {
        $secretaria->load('user');

        return Inertia::render('secretarias/edit', [
            'secretaria' => [
                'id'        => $secretaria->id,
                'profesion' => $secretaria->profesion,
            ],
            'user' => [
                'id'              => $secretaria->user->id,
                'name'            => $secretaria->user->name,
                'lastName'        => $secretaria->user->lastName,
                'ci'              => $secretaria->user->ci,
                'fechaNacimiento' => $secretaria->user->fechaNacimiento,
                'telefono'        => $secretaria->user->telefono,
                'email'           => $secretaria->user->email,
            ],
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ]);
    }

    public function update(Request $request, Secretaria $secretaria): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'profesion' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $secretaria->update($validated);

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó la profesión de la secretaria {$secretaria->user->name} {$secretaria->user->lastName} (id {$secretaria->id}) a '{$validated['profesion']}'.",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profesión actualizada exitosamente.')]);

        return to_route('Secretariasindex');
    }

    public function destroy(Request $request, Secretaria $secretaria): RedirectResponse
    {
        $secretaria->load('user');

        if ($secretaria->user->id === $request->user()->id) {
            return to_route('Secretariasindex')
                ->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        $nombreCompleto = trim("{$secretaria->user->name} {$secretaria->user->lastName}");
        $secretariaId = $secretaria->id;

        $secretaria->user->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó a la secretaria {$nombreCompleto} (id {$secretariaId}).",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Secretaria eliminada exitosamente.')]);

        return to_route('Secretariasindex');
    }
}