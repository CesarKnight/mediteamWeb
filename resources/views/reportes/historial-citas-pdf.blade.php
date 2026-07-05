<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de Citas</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; }

        .header { border-bottom: 2px solid #007585; padding-bottom: 10px; margin-bottom: 14px; }
        .header h1 { font-size: 18px; color: #007585; margin: 0 0 2px; }
        .header p { margin: 0; color: #6b7280; font-size: 10px; }

        .info-box { background: #f3f4f6; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 2px 6px; font-size: 10.5px; vertical-align: top; }
        .info-box td.label { color: #6b7280; width: 110px; }

        table.eventos { width: 100%; border-collapse: collapse; }
        table.eventos th {
            background: #eef2f2; text-align: left; font-size: 9.5px; text-transform: uppercase;
            color: #374151; padding: 5px 6px; border-bottom: 1px solid #d1d5db;
        }
        table.eventos td { padding: 6px; border-bottom: 1px solid #f0f0f0; vertical-align: top; font-size: 10.5px; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 9px; color: #fff; text-transform: uppercase; }
        .badge-aprobado { background: #059669; }
        .badge-pospuesto { background: #d97706; }
        .badge-cancelado { background: #dc2626; }

        .sin-citas { padding: 20px; text-align: center; color: #9ca3af; font-style: italic; }

        .footer { margin-top: 18px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Historial de Citas</h1>
        <p>Clínica Mediteam &mdash; Generado el {{ $generadoEn->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td class="label">Médico</td>
                <td>{{ $medico['name'] }} {{ $medico['lastName'] }}</td>
                <td class="label">CI</td>
                <td>{{ $medico['ci'] }}</td>
            </tr>
            <tr>
                <td class="label">Especialidad</td>
                <td>{{ $medico['especialidad'] ?? 'Sin especialidad' }}</td>
                <td class="label">Teléfono</td>
                <td>{{ $medico['telefono'] }}</td>
            </tr>
            <tr>
                <td class="label">Periodo del reporte</td>
                <td colspan="3">
                    {{ $fechaInicio ? \Illuminate\Support\Carbon::parse($fechaInicio)->format('d/m/Y') : 'Sin límite' }}
                    &mdash;
                    {{ $fechaFin ? \Illuminate\Support\Carbon::parse($fechaFin)->format('d/m/Y') : 'Sin límite' }}
                </td>
            </tr>
        </table>
    </div>

    @if ($citas->isEmpty())
        <div class="sin-citas">
            No se encontraron citas asignadas al médico en el periodo seleccionado.
        </div>
    @else
        <table class="eventos">
            <thead>
                <tr>
                    <th style="width: 130px;">Horario</th>
                    <th style="width: 65px;">Estado</th>
                    <th>Paciente</th>
                    <th>Servicio</th>
                    <th style="width: 60px;">Duración</th>
                    <th style="width: 60px;">Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($citas as $cita)
                    <tr>
                        <td>
                            {{ $cita['horaInicio']->format('d/m/Y H:i') }}
                            &mdash;
                            {{ $cita['horaFin']->format('H:i') }}
                        </td>
                        <td><span class="badge badge-{{ $cita['estado'] }}">{{ $cita['estado'] }}</span></td>
                        <td>{{ $cita['paciente']['name'] }} {{ $cita['paciente']['lastName'] }} (CI {{ $cita['paciente']['ci'] }})</td>
                        <td>{{ $cita['servicio']['titulo'] }}</td>
                        <td>{{ $cita['servicio']['duracion'] }} min</td>
                        <td>{{ number_format((float) $cita['servicio']['precio'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">Reporte generado automáticamente por el sistema Mediteam.</div>
</body>
</html>
