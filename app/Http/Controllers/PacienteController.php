<?php

namespace App\Http\Controllers;

use App\Enums\PacienteEstado;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::with('user')
            ->get()
            ->map(fn($p) => [
                'id'              => $p->id,
                'estado'          => $p->estado,
                'name'            => $p->user->name,
                'lastName'        => $p->user->lastName,
                'ci'              => $p->user->ci,
                'fechaNacimiento' => $p->user->fechaNacimiento,
                'telefono'        => $p->user->telefono,
                'email'           => $p->user->email,
            ]);

        return Inertia::render('pacientes/index', [
            'pacientes' => $pacientes,
        ]);
    }

    public function show(Paciente $paciente)
{
    $paciente->load('user');

    return Inertia::render('pacientes/show', [
        'paciente' => [
            'id'              => $paciente->id,
            'estado'          => $paciente->estado,
            'name'            => $paciente->user->name,
            'lastName'        => $paciente->user->lastName,
            'ci'              => $paciente->user->ci,
            'fechaNacimiento' => $paciente->user->fechaNacimiento,
            'telefono'        => $paciente->user->telefono,
            'email'           => $paciente->user->email,
        ],
    ]);
}
    public function edit(Paciente $paciente)
    {
        $paciente->load('user');

        return Inertia::render('pacientes/edit', [
            'paciente' => [
                'id'    => $paciente->id,
                'estado' => $paciente->estado,
            ],
            'user' => [
                'id'              => $paciente->user->id,
                'name'            => $paciente->user->name,
                'lastName'        => $paciente->user->lastName,
                'ci'              => $paciente->user->ci,
                'fechaNacimiento' => $paciente->user->fechaNacimiento,
                'telefono'        => $paciente->user->telefono,
                'email'           => $paciente->user->email,
            ],
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
            'estados'       => array_column(PacienteEstado::cases(), 'value'),
        ]);
    }


    public function updateEstado(Request $request, Paciente $paciente)
    {
        $validated = Validator::make($request->all(), [
            'estado' => ['required', Rule::enum(PacienteEstado::class)],
        ])->validate();

        $paciente->update(['estado' => $validated['estado']]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Estado actualizado exitosamente.')]);

        return to_route('Pacientesindex');
    }



    public function destroy(Request $request, Paciente $paciente)
    {
        $paciente->load('user');

        if ($paciente->user->id === $request->user()->id) {
            return redirect()->route('Pacientesindex')
                ->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        // Deleting the user cascades to paciente via the migration constraint
        $paciente->user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Paciente eliminado exitosamente.')]);

        return to_route('Pacientesindex');
    }
}
