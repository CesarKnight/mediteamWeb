<?php

namespace App\Enums;

enum UsuarioTipo: string{
    case Paciente = 'paciente';
    case Medico = 'medico';
    case Secretaria = 'secretaria';
    case Administrador = 'administrador';
}