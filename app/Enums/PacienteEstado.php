<?php

namespace App\Enums;

enum PacienteEstado: string{
    case Alta = 'alta';
    case Baja = 'baja';
}