<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SecretariaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PagoFacilCallbackController;
use App\Http\Controllers\PagoQrController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ReporteController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('usuarios')->name('Usuarios')->group(function () {
        Route::get('index', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('store', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit',    [UserController::class, 'edit'])->name('edit');
        Route::put('{user}',         [UserController::class, 'update'])->name('update');
        Route::delete('{user}/destroy', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pacientes')->name('Pacientes')->group(function () {
        Route::get('index',                      [PacienteController::class, 'index'])->name('index');
        Route::get('{paciente}/show',            [PacienteController::class, 'show'])->name('show');
        Route::get('{paciente}/edit',            [PacienteController::class, 'edit'])->name('edit');
        Route::put('{paciente}',                 [PacienteController::class, 'update'])->name('update');
        Route::patch('{paciente}/estado',        [PacienteController::class, 'updateEstado'])->name('updateEstado');
        Route::delete('{paciente}/destroy',      [PacienteController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('medicos')->name('Medicos')->group(function () {
        Route::get('index',                        [MedicoController::class, 'index'])->name('index');
        Route::get('{medico}/show',                [MedicoController::class, 'show'])->name('show');
        Route::get('{medico}/edit',                [MedicoController::class, 'edit'])->name('edit');
        Route::patch('{medico}/especialidad',      [MedicoController::class, 'updateEspecialidad'])->name('updateEspecialidad');
        Route::delete('{medico}/destroy',          [MedicoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('secretarias')->name('Secretarias')->group(function () {
        Route::get('index',                  [SecretariaController::class, 'index'])->name('index');
        Route::get('{secretaria}/show',      [SecretariaController::class, 'show'])->name('show');
        Route::get('{secretaria}/edit',      [SecretariaController::class, 'edit'])->name('edit');
        Route::patch('{secretaria}',         [SecretariaController::class, 'update'])->name('update');
        Route::delete('{secretaria}/destroy', [SecretariaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('historias')->name('Historias')->group(function () {
        Route::get('index',                [HistoriaController::class, 'index'])->name('index');
        Route::get('create',               [HistoriaController::class, 'create'])->name('create');
        Route::post('store',               [HistoriaController::class, 'store'])->name('store');
        Route::get('{historia}/show',      [HistoriaController::class, 'show'])->name('show');
        Route::patch('{historia}',         [HistoriaController::class, 'update'])->name('update');
        Route::delete('{historia}/destroy', [HistoriaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('historias/{historia}/consultas')->name('Consultas')->scopeBindings()->group(function () {
        Route::get('create',                [ConsultaController::class, 'create'])->name('create');
        Route::post('store',                [ConsultaController::class, 'store'])->name('store');
        Route::get('{consulta}/show',       [ConsultaController::class, 'show'])->name('show');
        Route::patch('{consulta}',          [ConsultaController::class, 'update'])->name('update');
        Route::delete('{consulta}/destroy', [ConsultaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('historias/{historia}/diagnosticos')->name('Diagnosticos')->scopeBindings()->group(function () {
        Route::post('store',                  [DiagnosticoController::class, 'store'])->name('store');
        Route::patch('{diagnostico}',         [DiagnosticoController::class, 'update'])->name('update');
        Route::delete('{diagnostico}/destroy', [DiagnosticoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('historias/{historia}/tratamientos')->name('Tratamientos')->scopeBindings()->group(function () {
        Route::post('store',                   [TratamientoController::class, 'store'])->name('store');
        Route::patch('{tratamiento}',          [TratamientoController::class, 'update'])->name('update');
        Route::delete('{tratamiento}/destroy', [TratamientoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('servicios')->name('Servicios')->group(function () {
        Route::get('index',                [ServicioController::class, 'index'])->name('index');
        Route::get('create',               [ServicioController::class, 'create'])->name('create');
        Route::post('store',               [ServicioController::class, 'store'])->name('store');
        Route::get('{servicio}/edit',      [ServicioController::class, 'edit'])->name('edit');
        Route::patch('{servicio}',         [ServicioController::class, 'update'])->name('update');
        Route::delete('{servicio}/destroy', [ServicioController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('citas')->name('Citas')->group(function () {
        Route::get('index',           [CitaController::class, 'index'])->name('index');
        Route::get('create',          [CitaController::class, 'create'])->name('create');
        Route::post('store',          [CitaController::class, 'store'])->name('store');
        Route::patch('{cita}/estado', [CitaController::class, 'updateEstado'])->name('updateEstado');
        Route::delete('{cita}/destroy', [CitaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('pagos')->name('Pagos')->group(function () {
        Route::get('index',         [PagoController::class, 'index'])->name('index');
        Route::get('create',        [PagoController::class, 'create'])->name('create');
        Route::post('store',        [PagoController::class, 'store'])->name('store');
        Route::get('{pago}/show',   [PagoController::class, 'show'])->name('show');
        Route::delete('{pago}/destroy', [PagoController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('reportes')->name('Reportes')->group(function () {
        Route::get('index',                       [ReporteController::class, 'index'])->name('index');
        Route::get('historial-paciente',          [ReporteController::class, 'historialPaciente'])->name('historialPaciente');
        Route::post('historial-paciente',         [ReporteController::class, 'generarHistorialPaciente'])->name('historialPaciente.generar');
        Route::get('historial-tratamientos',      [ReporteController::class, 'historialTratamientos'])->name('historialTratamientos');
        Route::post('historial-tratamientos',     [ReporteController::class, 'generarHistorialTratamientos'])->name('historialTratamientos.generar');
        Route::get('historial-medico',            [ReporteController::class, 'historialMedico'])->name('historialMedico');
        Route::post('historial-medico',           [ReporteController::class, 'generarHistorialMedico'])->name('historialMedico.generar');
        Route::get('historial-citas',             [ReporteController::class, 'historialCitas'])->name('historialCitas');
        Route::post('historial-citas',            [ReporteController::class, 'generarHistorialCitas'])->name('historialCitas.generar');
        Route::get('historial-pagos-paciente',    [ReporteController::class, 'historialPagosPaciente'])->name('historialPagosPaciente');
        Route::post('historial-pagos-paciente',   [ReporteController::class, 'generarHistorialPagosPaciente'])->name('historialPagosPaciente.generar');
    });

    Route::prefix('bitacora')->name('Bitacora')->group(function () {
        Route::get('index',         [BitacoraController::class, 'index'])->name('index');
    });

    Route::prefix('permisos')->name('Permisos')->group(function () {
        Route::get('index',              [PermisoController::class, 'index'])->name('index');
        Route::patch('{rol}/toggle',    [PermisoController::class, 'toggle'])->name('toggle');
    });

    Route::post('pagos/{pago}/pago-qr', [PagoQrController::class, 'store'])
        ->name('PagoQr.store');

    // External callback from PagoFácil's microservice — no CSRF, no auth
    Route::post('pagofacil/callback', [PagoFacilCallbackController::class, 'handle'])
        ->name('PagoFacil.callback')
        ->withoutMiddleware([PreventRequestForgery::class]);
});

require __DIR__ . '/settings.php';
