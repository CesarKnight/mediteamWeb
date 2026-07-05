<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de Paciente</title>
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

        .historia { margin-bottom: 16px; page-break-inside: avoid; }
        .historia-header {
            background: #007585; color: #fff; padding: 6px 10px; border-radius: 4px 4px 0 0;
            font-size: 11.5px;
        }
        .historia-header .estado { float: right; text-transform: uppercase; font-size: 9.5px; }
        .historia-meta { border: 1px solid #e5e7eb; border-top: none; padding: 6px 10px; font-size: 10px; color: #4b5563; }

        table.eventos { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.eventos th {
            background: #eef2f2; text-align: left; font-size: 9.5px; text-transform: uppercase;
            color: #374151; padding: 4px 6px; border-bottom: 1px solid #d1d5db;
        }
        table.eventos td { padding: 5px 6px; border-bottom: 1px solid #f0f0f0; vertical-align: top; font-size: 10.5px; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 9px; color: #fff; }
        .badge-consulta { background: #2563eb; }
        .badge-diagnostico { background: #d97706; }
        .badge-tratamiento { background: #059669; }

        .sin-eventos { padding: 10px; color: #9ca3af; font-style: italic; font-size: 10px; }
        .sin-historias { padding: 20px; text-align: center; color: #9ca3af; font-style: italic; }

        .footer { margin-top: 18px; font-size: 9px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Historial de Paciente</h1>
        <p>Clínica Mediteam &mdash; Generado el {{ $generadoEn->format('d/m/Y H:i') }}</p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td class="label">Paciente</td>
                <td>{{ $paciente['name'] }} {{ $paciente['lastName'] }}</td>
                <td class="label">CI</td>
                <td>{{ $paciente['ci'] }}</td>
            </tr>
            <tr>
                <td class="label">Fecha de nacimiento</td>
                <td>{{ \Illuminate\Support\Carbon::parse($paciente['fechaNacimiento'])->format('d/m/Y') }}</td>
                <td class="label">Teléfono</td>
                <td>{{ $paciente['telefono'] }}</td>
            </tr>
            <tr>
                <td class="label">Estado del paciente</td>
                <td>{{ ucfirst($paciente['estado']) }}</td>
                <td class="label">Periodo del reporte</td>
                <td>
                    {{ $fechaInicio ? \Illuminate\Support\Carbon::parse($fechaInicio)->format('d/m/Y') : 'Sin límite' }}
                    &mdash;
                    {{ $fechaFin ? \Illuminate\Support\Carbon::parse($fechaFin)->format('d/m/Y') : 'Sin límite' }}
                </td>
            </tr>
        </table>
    </div>

    @forelse ($historias as $historia)
        <div class="historia">
            <div class="historia-header">
                Historia Clínica #{{ $historia['id'] }} &mdash; {{ $historia['created_at']->format('d/m/Y H:i') }}
                <span class="estado">{{ $historia['estado'] }}</span>
            </div>
            <div class="historia-meta">
                Médico creador: {{ $historia['medicoCreador']['name'] }} {{ $historia['medicoCreador']['lastName'] }}
                ({{ $historia['medicoCreador']['especialidad'] ?? 'Sin especialidad' }})
                @if ($historia['medicosInvolucrados']->isNotEmpty())
                    &nbsp;|&nbsp; Médicos involucrados:
                    {{ $historia['medicosInvolucrados']->map(fn($m) => $m['name'].' '.$m['lastName'])->implode(', ') }}
                @endif
            </div>

            @if ($historia['eventos']->isEmpty())
                <div class="sin-eventos">Sin consultas, diagnósticos ni tratamientos registrados.</div>
            @else
                <table class="eventos">
                    <thead>
                        <tr>
                            <th style="width: 90px;">Fecha</th>
                            <th style="width: 90px;">Tipo</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($historia['eventos'] as $evento)
                            <tr>
                                <td>{{ $evento['fecha']->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($evento['tipo'] === 'consulta')
                                        <span class="badge badge-consulta">Consulta</span>
                                    @elseif ($evento['tipo'] === 'diagnostico')
                                        <span class="badge badge-diagnostico">Diagnóstico</span>
                                    @else
                                        <span class="badge badge-tratamiento">Tratamiento</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($evento['tipo'] === 'consulta')
                                        <strong>Motivo:</strong> {{ $evento['motivo'] }}
                                        &mdash; Peso: {{ $evento['peso'] }} kg, Altura: {{ $evento['altura'] }} cm
                                    @elseif ($evento['tipo'] === 'diagnostico')
                                        <strong>{{ $evento['enfermedad'] }}</strong> ({{ ucfirst($evento['gravedad']) }})
                                        &mdash; {{ $evento['diagnostico'] }}
                                    @else
                                        <strong>{{ $evento['medicamento'] }}</strong>
                                        &mdash; cada {{ $evento['frecuencia_horas'] }} horas
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @empty
        <div class="sin-historias">
            No se encontraron historias clínicas para el paciente en el periodo seleccionado.
        </div>
    @endforelse

    <div class="footer">Reporte generado automáticamente por el sistema Mediteam.</div>
</body>
</html>
