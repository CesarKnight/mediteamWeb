<?php

namespace App\Enums;

enum HistoriaEstado: string{
    case Pendiente = 'pendiente';
    case Anulado = 'anulado';
    case Finalizado = 'finalizado';
}