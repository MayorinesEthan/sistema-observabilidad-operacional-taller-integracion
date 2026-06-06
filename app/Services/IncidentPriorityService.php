<?php

namespace App\Services;

use App\Models\Alert;

class IncidentPriorityService
{
    public function calculate(Alert $alert): string
    {
        $clientPriority = $this->getClientPriority($alert);
        $metric = strtolower($alert->metric_name);
        $severity = strtolower($alert->severity);

        if ($clientPriority === 'high' && $severity === 'critical') {
            return 'critical';
        }

        if ($clientPriority === 'high' && str_contains($metric, 'disco')) {
            return 'critical';
        }

        if ($clientPriority === 'high' && $severity === 'warning') {
            return 'high';
        }

        if ($severity === 'critical') {
            return 'high';
        }

        if ($clientPriority === 'medium' && $severity === 'warning') {
            return 'medium';
        }

        return 'low';
    }

    public function recommendedAction(Alert $alert): string
    {
        $metric = strtolower($alert->metric_name);

        if (str_contains($metric, 'cpu')) {
            return 'Revisar procesos activos, consumo de recursos y servicios web asociados al servidor.';
        }

        if (str_contains($metric, 'memoria')) {
            return 'Revisar consumo de memoria, procesos en ejecución y posibles servicios saturados.';
        }

        if (str_contains($metric, 'disco')) {
            return 'Revisar espacio disponible, logs, archivos temporales, backups y crecimiento de archivos del sitio.';
        }

        return 'Revisar el estado general del servidor y validar el impacto sobre los sitios asociados.';
    }

    private function getClientPriority(Alert $alert): string
    {
        $server = $alert->server;

        if (!$server || $server->sites->count() === 0) {
            return 'medium';
        }

        $site = $server->sites->first();

        if (!$site || !$site->client) {
            return 'medium';
        }

        return strtolower($site->client->priority_level ?? 'medium');
    }
}
