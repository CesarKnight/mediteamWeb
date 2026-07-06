<?php

namespace App\Enums;

enum PermisoEnum: string
{
    case DashboardVer = 'Dashboard.ver';

    case UsuarioVer = 'Usuario.ver';
    case UsuarioCrear = 'Usuario.crear';
    case UsuarioEditar = 'Usuario.editar';
    case UsuarioEliminar = 'Usuario.eliminar';

    case PacienteVer = 'Paciente.ver';
    case PacienteEditar = 'Paciente.editar';
    case PacienteEliminar = 'Paciente.eliminar';

    case MedicoVer = 'Medico.ver';
    case MedicoEditar = 'Medico.editar';
    case MedicoEliminar = 'Medico.eliminar';

    case SecretariaVer = 'Secretaria.ver';
    case SecretariaEditar = 'Secretaria.editar';
    case SecretariaEliminar = 'Secretaria.eliminar';

    case HistoriaVer = 'Historia.ver';
    case HistoriaCrear = 'Historia.crear';
    case HistoriaEditar = 'Historia.editar';
    case HistoriaEliminar = 'Historia.eliminar';

    case ServicioVer = 'Servicio.ver';
    case ServicioCrear = 'Servicio.crear';
    case ServicioEditar = 'Servicio.editar';
    case ServicioEliminar = 'Servicio.eliminar';

    case CitaVer = 'Cita.ver';
    case CitaCrear = 'Cita.crear';
    case CitaEditar = 'Cita.editar';
    case CitaEliminar = 'Cita.eliminar';

    case PagoVer = 'Pago.ver';
    case PagoCrear = 'Pago.crear';
    case PagoEliminar = 'Pago.eliminar';
    case PagoGenerarQr = 'Pago.generarQr';

    case ReporteVer = 'Reporte.ver';

    case BitacoraVer = 'Bitacora.ver';
}
