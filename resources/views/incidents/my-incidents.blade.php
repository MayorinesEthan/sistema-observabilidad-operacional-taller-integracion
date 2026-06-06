<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis incidencias</title>
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

        main {
            padding: 30px 40px;
        }

        .nav {
            margin-bottom: 24px;
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav a, .btn {
            background: #102B42;
            color: white;
            padding: 10px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn-danger {
            background: #b91c1c;
        }

        .message {
            background: #dcfce7;
            color: #166534;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .incident-card {
            background: white;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .incident-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .priority-critical {
            color: #b91c1c;
            font-weight: bold;
        }

        .priority-high {
            color: #c2410c;
            font-weight: bold;
        }

        .priority-medium {
            color: #b45309;
            font-weight: bold;
        }

        .priority-low {
            color: #166534;
            font-weight: bold;
        }

        .status-open {
            color: #b45309;
            font-weight: bold;
        }

        .status-closed {
            color: #166534;
            font-weight: bold;
        }

        details {
            margin-top: 14px;
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
        }

        summary {
            cursor: pointer;
            font-weight: bold;
            color: #102B42;
        }

        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .actions {
            margin-top: 14px;
        }

        .empty {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        small {
            color: #555;
        }
    </style>
</head>
<body>

<header>
    <h1>Mis incidencias</h1>
    <p>Incidencias asignadas a {{ auth()->user()->name }}</p>
</header>

<main>

    <div class="nav">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <a href="{{ route('my-incidents.index') }}">Mis incidencias</a>
        <a href="{{ route('reports.operational') }}">Reporte operacional</a>

        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-danger">Cerrar sesión</button>
        </form>
    </div>

    @if(session('message'))
        <div class="message">{{ session('message') }}</div>
    @endif

    @if($incidents->count() > 0)
        @foreach($incidents as $incident)
            <div class="incident-card">
                <div class="incident-header">
                    <div>
                        <h2>{{ $incident->title }}</h2>
                        <p>
                            Alerta: <strong>{{ $incident->alert->metric_name ?? 'Sin alerta' }}</strong>
                        </p>

                        <p>
                            Cliente / sitio:
                            @if($incident->alert && $incident->alert->server && $incident->alert->server->sites->count() > 0)
                                @foreach($incident->alert->server->sites as $site)
                                    <strong>{{ $site->client->name ?? 'Cliente no registrado' }}</strong>
                                    <small>{{ $site->domain }}</small>
                                @endforeach
                            @else
                                Sin sitio asociado
                            @endif
                        </p>
                    </div>

                    <div>
                        <p>Prioridad:
                            <span class="priority-{{ $incident->priority ?? 'medium' }}">
                                {{ $incident->priority ?? 'medium' }}
                            </span>
                        </p>

                        <p>Estado:
                            <span class="{{ $incident->status === 'open' ? 'status-open' : 'status-closed' }}">
                                {{ $incident->status }}
                            </span>
                        </p>

                        <p>
                            Tiempo resolución:
                            @if($incident->resolution_time_minutes)
                                {{ $incident->resolution_time_minutes }} min
                            @else
                                Pendiente
                            @endif
                        </p>
                    </div>
                </div>

                <details>
                    <summary>Ver historial de acciones</summary>

                    @if($incident->logs->count() > 0)
                        @foreach($incident->logs as $log)
                            <p>
                                <strong>{{ $log->action }}</strong><br>
                                <small>{{ $log->comment }}</small><br>
                                <small>{{ $log->created_at }}</small>
                            </p>
                            <hr>
                        @endforeach
                    @else
                        <p>Sin registros en bitácora.</p>
                    @endif
                </details>

                @if($incident->status === 'open')
                    <details>
                        <summary>Registrar nueva acción</summary>

                        <form method="POST" action="{{ route('incidents.logs.store', $incident->id) }}">
                            @csrf

                            <label>Acción realizada</label>
                            <input type="text" name="action" required placeholder="Ej: Revisión de procesos">

                            <label>Comentario técnico</label>
                            <textarea name="comment" placeholder="Detalle de la acción realizada"></textarea>

                            <button type="submit" class="btn">Registrar acción</button>
                        </form>
                    </details>

                    <div class="actions">
                        <form method="POST" action="{{ route('incidents.close', $incident->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Cerrar incidencia</button>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="empty">
            No tienes incidencias asignadas.
        </div>
    @endif

</main>

</body>
</html>
