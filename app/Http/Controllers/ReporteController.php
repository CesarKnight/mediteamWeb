<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ReporteController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('reportes/index');
    }

    public function historialPaciente(): Response
    {
        return Inertia::render('reportes/historial-paciente');
    }

    public function historialTratamientos(): Response
    {
        return Inertia::render('reportes/historial-tratamientos');
    }

    public function historialMedico(): Response
    {
        return Inertia::render('reportes/historial-medico');
    }

    public function historialCitas(): Response
    {
        return Inertia::render('reportes/historial-citas');
    }

    public function historialPagosPaciente(): Response
    {
        return Inertia::render('reportes/historial-pagos-paciente');
    }
}
