<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Operacional</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
            margin: 0;
        }

        header {
            background: #102B42;
            color: white;
            padding: 28px 40px;
        }

        header h1 {
            margin: 0;
            font-size: 30px;
        }

        header p {
            margin: 8px 0 0;
        }

        main {
            padding: 30px 40px;
        }

        h2 {
            color: #102B42;
            margin-top: 34px;
            margin-bottom: 14px;
        }

        .summary {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            line-height: 1.6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin: 0;
            font-size: 30px;
            color: #102B42;
        }

        .card p {
            margin: 8px 0 0;
            color: #555;
        }

        .report-card {
            background: white;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            page-break-inside: avoid;
        }

        .report-card h3 {
            margin-top: 0;
            margin-bottom: 14px;
            color: #102B42;
        }

        .report-row {
            display: grid;
            grid-template-columns: 170px 1fr;
            gap: 10px;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .report-label {
            font-weight: bold;
            color: #102B42;
        }

        .report-value {
            color: #222;
        }

        .actions {
            margin-bottom: 24px;
        }

        .btn {
            display: inline-block;
            background: #102B42;
            color: white;
            padding: 10px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 8px;
        }

        .print-btn {
            background: #166534;
        }

        small {
            color: #555;
        }

        .critical {
            color: #b91c1c;
            font-weight: bold;
        }

        .high {
            color: #c2410c;
            font-weight: bold;
        }

        .medium {
            color: #b45309;
            font-weight: bold;
        }

        .low {
            color: #166534;
            font-weight: bold;
        }

        .open {
            color: #b45309;
            font-weight: bold;
        }

        .closed {
            color: #166534;
            font-weight: bold;
        }

        .warning {
            color: #b45309;
            font-weight: bold;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 12mm;
            }

            body {
                background: white;
                font-size: 11px;
            }

            header {
                background: #102B42 !important;
                color: white !important;
                padding: 16px 20px;
            }

            header h1 {
                font-size: 20px;
            }

            header p {
                font-size: 11px;
            }

            main {
                padding: 14px 18px;
            }

            h2 {
                font-size: 15px;
                margin-top: 16px;
                margin-bottom: 8px;
            }

            .actions {
                display: none;
            }

            .card,
            .summary,
            .report-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .summary {
                padding: 12px;
                line-height: 1.35;
                margin-bottom: 14px;
            }

            .grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                margin-bottom: 16px;
            }

            .card {
                padding: 10px;
            }

            .card h3 {
                font-size: 20px;
            }

            .card p {
                font-size: 10px;
            }

            .report-card {
                padding: 10px;
                margin-bottom: 10px;
                border-radius: 0;
                page-break-inside: avoid;
            }

            .report-card h3 {
                font-size: 13px;
                margin-bottom: 8px;
            }

            .report-row {
                grid-template-columns: 120px 1fr;
                gap: 6px;
                margin-bottom: 5px;
                font-size: 10px;
            }

            .report-label,
            .report-value {
                font-size: 10px;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            small {
                font-size: 8px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Reporte Operacional del Sistema de Observabilidad</h1>
    <p>Generado el {{ $now->format('d-m-Y H:i:s') }}</p>
    <p>ID de reporte: {{ $report->id }}</p>
</header>

<main>

    <div class="actions">
        <a href="/dashboard" class="btn">Volver al dashboard</a>
        <a href="#" onclick="window.print()" class="btn print-btn">Imprimir / Guardar como PDF</a>
    </div>

    <section class="summary">
        <h2>Resumen ejecutivo operacional</h2>

        <p>
            Este reporte consolida información operacional registrada por el sistema web de observabilidad.
            A diferencia de un panel técnico de métricas, este documento resume alertas, incidentes,
            clientes y sitios afectados, prioridades y acciones registradas en bitácora.
        </p>

        <p>
            Durante el periodo revisado, el sistema registra {{ $totalAlerts }} alertas y {{ $totalIncidents }} incidentes.
            Actualmente existen {{ $openAlerts }} alertas abiertas y {{ $openIncidents }} incidentes abiertos.
        </p>
    </section>

    <section>
        <h2>Indicadores generales</h2>

        <div class="grid">
            <div class="card">
                <h3>{{ $totalClients }}</h3>
                <p>Clientes registrados</p>
            </div>

            <div class="card">
                <h3>{{ $totalSites }}</h3>
                <p>Sitios monitoreados</p>
            </div>

            <div class="card">
                <h3>{{ $totalAlerts }}</h3>
                <p>Total alertas</p>
            </div>

            <div class="card">
                <h3>{{ $openAlerts }}</h3>
                <p>Alertas abiertas</p>
            </div>

            <div class="card">
                <h3>{{ $totalIncidents }}</h3>
                <p>Total incidentes</p>
            </div>

            <div class="card">
                <h3>{{ $openIncidents }}</h3>
                <p>Incidentes abiertos</p>
            </div>

            <div class="card">
                <h3>
                    @if($avgResolutionTime)
                        {{ number_format($avgResolutionTime, 1) }} min
                    @else
                        N/D
                    @endif
                </h3>
                <p>Tiempo promedio de resolución</p>
            </div>
        </div>
    </section>

    <section>
        <h2>Incidentes por prioridad</h2>

        <div class="grid">
            <div class="card">
                <h3 class="critical">{{ $criticalIncidents }}</h3>
                <p>Críticos</p>
            </div>

            <div class="card">
                <h3 class="high">{{ $highIncidents }}</h3>
                <p>Altos</p>
            </div>

            <div class="card">
                <h3 class="medium">{{ $mediumIncidents }}</h3>
                <p>Medios</p>
            </div>

            <div class="card">
                <h3 class="low">{{ $lowIncidents }}</h3>
                <p>Bajos</p>
            </div>
        </div>
    </section>

    <section>
        <h2>Alertas recientes contextualizadas</h2>

        @if($recentAlerts->count() > 0)
            @foreach($recentAlerts as $alert)
                <div class="report-card">
                    <h3>Alerta: {{ $alert->metric_name }}</h3>

                    <div class="report-row">
                        <div class="report-label">Servidor:</div>
                        <div class="report-value">
                            {{ $alert->server->name ?? 'Sin servidor' }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Cliente / sitio:</div>
                        <div class="report-value">
                            @if($alert->server && $alert->server->sites->count() > 0)
                                @foreach($alert->server->sites as $site)
                                    <strong>{{ $site->client->name ?? 'Cliente no registrado' }}</strong><br>
                                    <small>{{ $site->domain }}</small><br>
                                @endforeach
                            @else
                                Sin sitio asociado
                            @endif
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Valor registrado:</div>
                        <div class="report-value">
                            {{ $alert->metric_value }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Umbral definido:</div>
                        <div class="report-value">
                            {{ $alert->threshold_value }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Severidad:</div>
                        <div class="report-value {{ $alert->severity }}">
                            {{ $alert->severity }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Estado:</div>
                        <div class="report-value {{ $alert->status }}">
                            {{ $alert->status }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Fecha:</div>
                        <div class="report-value">
                            {{ $alert->created_at }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p>No existen alertas registradas.</p>
        @endif
    </section>

    <section>
        <h2>Incidentes recientes y bitácora</h2>

        @if($recentIncidents->count() > 0)
            @foreach($recentIncidents as $incident)
                <div class="report-card">
                    <h3>{{ $incident->title }}</h3>

                    <div class="report-row">
                        <div class="report-label">Alerta asociada:</div>
                        <div class="report-value">
                            {{ $incident->alert->metric_name ?? 'Sin alerta' }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Prioridad:</div>
                        <div class="report-value {{ $incident->priority ?? 'medium' }}">
                            {{ $incident->priority ?? 'medium' }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Responsable:</div>
                        <div class="report-value">
                            {{ $incident->assigned_to ?? 'Sin asignar' }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Estado:</div>
                        <div class="report-value {{ $incident->status }}">
                            {{ $incident->status }}
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Tiempo resolución:</div>
                        <div class="report-value">
                            @if($incident->resolution_time_minutes)
                                {{ $incident->resolution_time_minutes }} min
                            @else
                                Pendiente
                            @endif
                        </div>
                    </div>

                    <div class="report-row">
                        <div class="report-label">Bitácora:</div>
                        <div class="report-value">
                            @if($incident->logs->count() > 0)
                                @foreach($incident->logs as $log)
                                    <strong>{{ $log->action }}</strong><br>
                                    <small>{{ $log->comment }}</small><br>
                                    <small>{{ $log->created_at }}</small><br><br>
                                @endforeach
                            @else
                                Sin registros
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p>No existen incidentes registrados.</p>
        @endif
    </section>

    <section class="summary">
        <h2>Conclusión operacional</h2>

        @if($openIncidents > 0 || $openAlerts > 0)
            <p>
                El sistema mantiene eventos abiertos que requieren revisión técnica. Se recomienda priorizar los
                incidentes críticos y altos, revisar la bitácora de acciones y validar el estado de los sitios
                e-commerce asociados.
            </p>
        @else
            <p>
                Al momento de generar el reporte no existen incidentes o alertas abiertas registradas en el sistema.
                Los eventos recientes se encuentran cerrados y cuentan con trazabilidad operacional registrada.
            </p>
        @endif

        <p>
            Este reporte fue generado desde la base de datos operacional del sistema, integrando información de
            clientes, sitios, alertas, incidentes y bitácoras. Su propósito es documentar la gestión posterior a la
            alerta técnica, entregando evidencia del proceso de atención.
        </p>
    </section>

</main>

</body>
</html>
