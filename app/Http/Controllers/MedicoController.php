<?php

namespace App\Http\Controllers;

use App\Concerns\ProfileValidationRules;
use App\Models\Medico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class MedicoController extends Controller
{
    use ProfileValidationRules;

    public function index(): Response
    {
        $medicos = Medico::with('user')
            ->get()
            ->map(fn($m) => [
                'id'           => $m->id,
                'especialidad' => $m->especialidad,
                'user' => [
                    'id'              => $m->user->id,
                    'name'            => $m->user->name,
                    'lastName'        => $m->user->lastName,
                    'ci'              => $m->user->ci,
                    'fechaNacimiento' => $m->user->fechaNacimiento,
                    'telefono'        => $m->user->telefono,
                    'email'           => $m->user->email,
                ],
            ]);

        return Inertia::render('medicos/index', [
            'medicos' => $medicos,
        ]);
    }

    public function show(Medico $medico): Response
    {
        $medico->load('user');

        return Inertia::render('medicos/show', [
            'medico' => [
                'id'           => $medico->id,
                'especialidad' => $medico->especialidad,
            ],
            'user' => [
                'id'              => $medico->user->id,
                'name'            => $medico->user->name,
                'lastName'        => $medico->user->lastName,
                'ci'              => $medico->user->ci,
                'fechaNacimiento' => $medico->user->fechaNacimiento,
                'telefono'        => $medico->user->telefono,
                'tipo'              => $medico->user->tipo,
                'email'           => $medico->user->email,
            ],
        ]);
    }

    public function edit(Medico $medico): Response
    {
        $medico->load('user');

        return Inertia::render('medicos/edit', [
            'medico' => [
                'id'           => $medico->id,
                'especialidad' => $medico->especialidad,
            ],
            'user' => [
                'id'              => $medico->user->id,
                'name'            => $medico->user->name,
                'lastName'        => $medico->user->lastName,
                'ci'              => $medico->user->ci,
                'fechaNacimiento' => $medico->user->fechaNacimiento,
                'telefono'        => $medico->user->telefono,
                'tipo'              => $medico->user->tipo,
                'email'           => $medico->user->email,
            ],
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ]);
    }

    public function updateEspecialidad(Request $request, Medico $medico): RedirectResponse
    {
        $validated = Validator::make($request->all(), [
            'especialidad' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $medico->update(['especialidad' => $validated['especialidad']]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Especialidad actualizada exitosamente.')]);

        return to_route('Medicosindex');
    }

    public function destroy(Request $request, Medico $medico): RedirectResponse
    {
        $medico->load('user');

        if ($medico->user->id === $request->user()->id) {
            return to_route('Medicosindex')
                ->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        $medico->user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Médico eliminado exitosamente.')]);

        return to_route('Medicosindex');
    }
}
