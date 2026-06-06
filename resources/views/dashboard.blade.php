<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Observabilidad Operacional</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            color: #222;
        }

        header {
            background: #102B42;
            color: white;
            padding: 24px 40px;
        }

        header h1 {
            margin: 0;
            font-size: 32px;
        }

        header p {
            margin-top: 12px;
            margin-bottom: 0;
            font-size: 16px;
        }

        main {
            padding: 30px 40px;
        }

        h2 {
            margin-top: 28px;
            margin-bottom: 10px;
            color: #102B42;
        }

        .nav {
            margin-bottom: 24px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav a,
        .btn {
            background: #102B42;
            color: white;
            padding: 10px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .nav a.green {
            background: #166534;
        }

        .nav a.gray {
            background: #525252;
        }

        .btn-danger {
            background: #b91c1c;
        }

        .user-info {
            margin-left: auto;
            color: #333;
        }

        .message {
            background: #dcfce7;
            color: #166534;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section-note {
            margin-top: 0;
            margin-bottom: 16px;
            color: #555;
            font-size: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .site-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .card,
        .site-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card h2 {
            margin: 0;
            font-size: 32px;
            color: #102B42;
        }

        .card p {
            margin: 8px 0 0;
            color: #555;
        }

        .site-card h3 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #102B42;
        }

        .site-meta {
            color: #555;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .site-metric-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 8px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .site-metric-row:last-child {
            border-bottom: none;
        }

        .metric-label {
            font-weight: bold;
            color: #102B42;
        }

        .metric-value {
            color: #222;
        }

        .available {
            color: #166534;
            font-weight: bold;
        }

        .unavailable {
            color: #b91c1c;
            font-weight: bold;
        }

        .unknown {
            color: #b45309;
            font-weight: bold;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            background: #102B42;
            color: white;
        }

        small {
            color: #555;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .status-open {
            color: #b45309;
            font-weight: bold;
        }

        .status-closed {
            color: #166534;
            font-weight: bold;
        }

        .severity-warning {
            color: #b45309;
            font-weight: bold;
        }

        .severity-critical {
            color: #b91c1c;
            font-weight: bold;
        }

        .empty {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<header>
    <h1>Sistema Web de Observabilidad Operacional</h1>
    <p>Dashboard de métricas del servidor, métricas por sitio y alertas contextualizadas</p>
</header>

<main>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>

        <a href="{{ route('my-incidents.index') }}" class="green">
            Mis incidencias
        </a>

        <a href="{{ route('reports.operational') }}" class="gray">
            Reporte operacional
        </a>

        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-danger">
                Cerrar sesión
            </button>
        </form>

        <span class="user-info">
            Usuario: <strong>{{ auth()->user()->name }}</strong>
        </span>
    </div>

    @if(session('message'))
        <div class="message">
            {{ session('message') }}
        </div>
    @endif

    @if($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <section>
        <div class="grid">
            <div class="card">
                <h2>{{ $totalServers }}</h2>
                <p>Servidores</p>
            </div>

            <div class="card">
                <h2>{{ $totalSites }}</h2>
                <p>Sitios monitoreados</p>
            </div>

            <div class="card">
                <h2>{{ $totalClients }}</h2>
                <p>Clientes registrados</p>
            </div>

            <div class="card">
                <h2>{{ $highPriorityClients }}</h2>
                <p>Clientes alta prioridad</p>
            </div>

            <div class="card">
                <h2>{{ $openAlerts }}</h2>
                <p>Alertas abiertas</p>
            </div>

            <div class="card">
                <h2>{{ $openIncidents }}</h2>
                <p>Incidencias abiertas</p>
            </div>

            <div class="card">
                <h2>{{ $totalReports }}</h2>
                <p>Reportes generados</p>
            </div>
        </div>
    </section>

    <section>
        <h2>Nivel 1: métricas generales del servidor</h2>
        <p class="section-note">
            Métricas técnicas consultadas desde Prometheus y Node Exporter. Representan el estado general del servidor completo.
        </p>

        <div class="metrics-grid">
            <div class="card">
                <h2>
                    @if($serverStatus['success'])
                        OK
                    @else
                        Error
                    @endif
                </h2>
                <p>Estado API Prometheus</p>
            </div>

            <div class="card">
                <h2>
                    @if($cpuUsage['success'] && count($cpuUsage['data']['data']['result']) > 0)
                        {{ number_format($cpuUsage['data']['data']['result'][0]['value'][1], 2) }}%
                    @else
                        N/D
                    @endif
                </h2>
                <p>Uso CPU del servidor</p>
            </div>

            <div class="card">
                <h2>
                    @if($memoryAvailable['success'] && count($memoryAvailable['data']['data']['result']) > 0)
                        {{ number_format($memoryAvailable['data']['data']['result'][0]['value'][1] / 1024 / 1024 / 1024, 2) }} GB
                    @else
                        N/D
                    @endif
                </h2>
                <p>Memoria disponible del servidor</p>
            </div>

            <div class="card">
                <h2>
                    @if($diskAvailable['success'] && count($diskAvailable['data']['data']['result']) > 0)
                        {{ number_format($diskAvailable['data']['data']['result'][0]['value'][1] / 1024 / 1024 / 1024, 2) }} GB
                    @else
                        N/D
                    @endif
                </h2>
                <p>Disco disponible del servidor</p>
            </div>
        </div>
    </section>

    <section>
        <h2>Nivel 2: métricas individuales por sitio</h2>
        <p class="section-note">
            Métricas calculadas por Laravel para cada sitio registrado. Incluyen disponibilidad, código HTTP, tiempo de respuesta y uso de disco por carpeta.
        </p>

        @if($sitesWithMetrics->count() > 0)
            <div class="site-grid">
                @foreach($sitesWithMetrics as $site)
                    @php
                        $metric = $site->latestMetric;
                    @endphp

                    <div class="site-card">
                        <h3>{{ $site->name }}</h3>

                        <div class="site-meta">
                            <strong>Cliente:</strong> {{ $site->client->name ?? 'Cliente no registrado' }}<br>
                            <strong>Dominio:</strong> {{ $site->domain }}<br>
                            <strong>URL:</strong> {{ $site->url ?? 'URL no registrada' }}<br>
                            <strong>Servidor:</strong> {{ $site->server->name ?? 'Servidor no registrado' }}
                        </div>

                        @if($metric)
                            <div class="site-metric-row">
                                <div class="metric-label">Disponibilidad:</div>
                                <div class="metric-value {{ $metric->availability_status }}">
                                    {{ $metric->availability_status }}
                                </div>
                            </div>

                            <div class="site-metric-row">
                                <div class="metric-label">Código HTTP:</div>
                                <div class="metric-value">
                                    {{ $metric->http_status ?? 'N/D' }}
                                </div>
                            </div>

                            <div class="site-metric-row">
                                <div class="metric-label">Tiempo respuesta:</div>
                                <div class="metric-value">
                                    @if($metric->response_time_ms)
                                        {{ number_format($metric->response_time_ms, 2) }} ms
                                    @else
                                        N/D
                                    @endif
                                </div>
                            </div>

                            <div class="site-metric-row">
                                <div class="metric-label">Uso de disco:</div>
                                <div class="metric-value">
                                    @if($metric->disk_usage_mb)
                                        {{ number_format($metric->disk_usage_mb, 2) }} MB
                                    @else
                                        N/D
                                    @endif
                                </div>
                            </div>

                            <div class="site-metric-row">
                                <div class="metric-label">Última revisión:</div>
                                <div class="metric-value">
                                    {{ $metric->checked_at }}
                                </div>
                            </div>
                        @else
                            <div class="empty">
                                Este sitio aún no tiene métricas registradas. Ejecuta:
                                <br>
                                <strong>php artisan site-metrics:check</strong>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty">
                No existen sitios activos registrados.
            </div>
        @endif
    </section>

    <section>
        <h2>Alertas recientes contextualizadas</h2>
        <p class="section-note">
            Alertas técnicas enriquecidas con cliente y sitio e-commerce potencialmente afectado. Desde esta sección se puede crear una incidencia y asignarla a un integrante del equipo.
        </p>

        @if($recentAlerts->count() > 0)
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Servidor</th>
                            <th>Cliente / Sitio afectado</th>
                            <th>Métrica</th>
                            <th>Valor</th>
                            <th>Umbral</th>
                            <th>Severidad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Asignación</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($recentAlerts as $alert)
                            <tr>
                                <td>{{ $alert->server->name ?? 'Sin servidor' }}</td>

                                <td>
                                    @if($alert->server && $alert->server->sites->count() > 0)
                                        @foreach($alert->server->sites as $site)
                                            <strong>{{ $site->client->name ?? 'Cliente no registrado' }}</strong><br>
                                            <small>{{ $site->domain }}</small><br>
                                        @endforeach
                                    @else
                                        Sin sitio asociado
                                    @endif
                                </td>

                                <td>{{ $alert->metric_name }}</td>
                                <td>{{ $alert->metric_value }}</td>
                                <td>{{ $alert->threshold_value }}</td>

                                <td class="{{ $alert->severity === 'critical' ? 'severity-critical' : 'severity-warning' }}">
                                    {{ $alert->severity }}
                                </td>

                                <td class="{{ $alert->status === 'open' ? 'status-open' : 'status-closed' }}">
                                    {{ $alert->status }}
                                </td>

                                <td>{{ $alert->created_at }}</td>

                                <td>
                                    @if($alert->status === 'open')
                                        @if($teamUsers->count() > 0)
                                            <form method="POST" action="{{ route('alerts.incident', $alert->id) }}">
                                                @csrf

                                                <select name="assigned_user_id" required>
                                                    <option value="">Asignar a...</option>
                                                    @foreach($teamUsers as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn">
                                                    Crear incidencia
                                                </button>
                                            </form>
                                        @else
                                            No existen usuarios registrados.
                                        @endif
                                    @else
                                        Alerta cerrada
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty">
                No existen alertas registradas.
            </div>
        @endif
    </section>

</main>

</body>
</html>
