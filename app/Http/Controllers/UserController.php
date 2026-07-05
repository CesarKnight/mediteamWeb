<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Concerns\PasswordValidationRules;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Validator;
use App\Concerns\ProfileValidationRules;
use App\Enums\PacienteEstado;
use App\Enums\UsuarioTipo;
use App\Services\BitacoraService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ProfileValidationRules, PasswordValidationRules;

    public function __construct(private readonly BitacoraService $bitacora) {}

    public function index(Request $request): Response{
        $usuarios = User::all();
        
        return Inertia::render('usuarios/index', [
            'usuarios' => $usuarios
        ]);
    }

    public function create(Request $request): Response{    
        return Inertia::render('usuarios/create', [
            'passwordRules' => \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString(),
        ]);
    }

    public function store(Request $request, CreateNewUser $creator): RedirectResponse
    {
        $usuario = $creator->create($request->all());

        if (UsuarioTipo::tryFrom($request->input('tipo')) === UsuarioTipo::Paciente) {
            $paciente = $usuario->paciente();
            $paciente->create([
                'estado' => PacienteEstado::Baja,
            ]);
        }

        if (UsuarioTipo::tryFrom($request->input('tipo')) === UsuarioTipo::Medico) {
            $usuario->medico()->create([
                'especialidad' => 'pendiente',
            ]);
        }

        if (UsuarioTipo::tryFrom($request->input('tipo')) === UsuarioTipo::Secretaria) {
            $usuario->secretaria()->create([
                'profesion' => 'pendiente',
            ]);
        }

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " creó al usuario {$usuario->name} {$usuario->lastName} (id {$usuario->id}, tipo {$usuario->tipo->value}).",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Usuario creado Exitosamente.')]);

        return to_route('Usuariosindex');
    }

    public function edit(User $user)
    {
        return Inertia::render('usuarios/edit', [
            'user' => $user->only('id', 'name', 'lastName', 'ci', 'fechaNacimiento', 'telefono', 'tipo', 'email'),
            'passwordRules' => \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        // profileRules($user->id) makes the unique email rule ignore this user's own email
        $validated = Validator::make($request->all(), [
            ...$this->profileRules($user->id),
        ])->validate();

        $user->update(collect($validated)->except(['password', 'password_confirmation'])->all());

        $passwordChanged = false;
        if ($request->filled('password')) {
            $validated = Validator::make($request->all(), [
                'password' => $this->passwordRules(),
            ])->validate();

            $user->update(['password' => $request->password]);
            $passwordChanged = true;
        }

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " actualizó al usuario {$user->name} {$user->lastName} (id {$user->id})."
                . ($passwordChanged ? ' Contraseña actualizada.' : ''),
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Usuario actualizado Exitosamente.')]);

        return redirect()->back();
    }
    
    public function destroy(Request $request, User $user):RedirectResponse{
        // Prevent admin from deleting themselves
        $currentUser = $request->user();

        if ($user->id === $currentUser['id']) {
            return redirect()->route('users.index')
                ->withErrors(['error' => 'No puedes eliminar tu propia cuenta.']);
        }

        $nombreCompleto = trim("{$user->name} {$user->lastName}");
        $userId = $user->id;

        $user->delete();

        $this->bitacora->registrar(
            "Usuario con id " . Auth::id() . " eliminó al usuario {$nombreCompleto} (id {$userId}).",
            static::class,
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Usuario eliminado Exitosamente.')]);
        return redirect()->back();
    }
}
