<?php

use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DiagnosticoController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SecretariaController;
use App\Http\Controllers\TratamientoController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

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
});

require __DIR__ . '/settings.php';
