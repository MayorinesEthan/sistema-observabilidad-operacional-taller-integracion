<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\User;
use App\Services\IncidentPriorityService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class IncidentController extends Controller
{
    public function store(Request $request, $alertId, IncidentPriorityService $priorityService)
    {
        $request->validate([
            'assigned_user_id' => ['required', 'exists:users,id']
        ]);

        $alert = Alert::with('server.sites.client')->findOrFail($alertId);

        $existingIncident = Incident::where('alert_id', $alert->id)
            ->where('status', 'open')
            ->first();

        if ($existingIncident) {
            return redirect('/dashboard')->with('message', 'Ya existe un incidente abierto para esta alerta.');
        }

        $assignedUser = User::findOrFail($request->assigned_user_id);

        $priority = $priorityService->calculate($alert);
        $recommendedAction = $priorityService->recommendedAction($alert);

        $incident = Incident::create([
            'alert_id' => $alert->id,
            'title' => 'Incidente asociado a ' . $alert->metric_name,
            'description' => $this->buildDescription($alert, $recommendedAction),
            'status' => 'open',
            'priority' => $priority,
            'assigned_to' => $assignedUser->name,
            'assigned_user_id' => $assignedUser->id,
            'action_taken' => $recommendedAction
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => 'Incidente creado',
            'comment' => 'Incidente generado desde alerta ' . $alert->metric_name . ' con prioridad automática ' . $priority . '.'
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => 'Incidente asignado',
            'comment' => 'Incidente asignado a ' . $assignedUser->name . '.'
        ]);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => 'Acción sugerida',
            'comment' => $recommendedAction
        ]);

        return redirect('/dashboard')->with('message', 'Incidente creado y asignado a ' . $assignedUser->name . ' con prioridad ' . $priority . '.');
    }

    public function close($incidentId)
    {
        $incident = Incident::with('alert')->findOrFail($incidentId);

        $createdAt = Carbon::parse($incident->created_at, 'America/Santiago');
        $closedAt = Carbon::now('America/Santiago');

        $resolutionTime = max(1, $createdAt->diffInMinutes($closedAt));

        $incident->update([
            'status' => 'closed',
            'resolution' => 'Incidente cerrado desde el sistema.',
            'closed_at' => $closedAt,
            'resolved_at' => $closedAt,
            'resolution_time_minutes' => $resolutionTime
        ]);

        if ($incident->alert) {
            $incident->alert->update([
                'status' => 'closed'
            ]);
        }

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => 'Incidente cerrado',
            'comment' => 'El incidente fue cerrado. Tiempo de resolución: ' . $resolutionTime . ' minutos.'
        ]);

        return redirect()->back()->with('message', 'Incidente y alerta asociada cerrados correctamente.');
    }

    private function buildDescription(Alert $alert, string $recommendedAction): string
    {
        $affectedContext = 'No se encontró cliente o sitio asociado.';

        if ($alert->server && $alert->server->sites->count() > 0) {
            $site = $alert->server->sites->first();
            $clientName = $site->client->name ?? 'Cliente no registrado';
            $siteDomain = $site->domain ?? 'Dominio no registrado';

            $affectedContext = "Cliente afectado: {$clientName}. Sitio afectado: {$siteDomain}.";
        }

        return $alert->message .
            ' Valor registrado: ' . $alert->metric_value .
            '. Umbral definido: ' . $alert->threshold_value .
            '. ' . $affectedContext .
            ' Acción sugerida: ' . $recommendedAction;
    }
}
