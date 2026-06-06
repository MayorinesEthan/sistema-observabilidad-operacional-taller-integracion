<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Server;
use App\Models\Alert;
use App\Services\PrometheusService;

class CheckServerAlerts extends Command
{
    protected $signature = 'alerts:check';

    protected $description = 'Consulta métricas desde Prometheus y registra alertas si se superan los umbrales definidos.';

    public function handle(PrometheusService $prometheus)
    {
        $this->info('Iniciando revisión de métricas...');

        $server = Server::first();

        if (!$server) {
            $this->error('No existe un servidor registrado en la base de datos.');
            return Command::FAILURE;
        }

        $cpuThreshold = 80;
        $memoryThreshold = 1;
        $diskThreshold = 5;

        $cpuUsage = $prometheus->getCpuUsage();
        $memoryAvailable = $prometheus->getMemoryAvailable();
        $diskAvailable = $prometheus->getDiskAvailable();

        if ($cpuUsage['success'] && count($cpuUsage['data']['data']['result']) > 0) {
            $cpuValue = round((float) $cpuUsage['data']['data']['result'][0]['value'][1], 2);

            if ($cpuValue >= $cpuThreshold) {
                $this->createAlert(
                    $server->id,
                    'CPU',
                    $cpuValue,
                    $cpuThreshold,
                    'critical',
                    'El uso de CPU superó el umbral definido.'
                );
            }

            $this->info("CPU revisada: {$cpuValue}%");
        }

        if ($memoryAvailable['success'] && count($memoryAvailable['data']['data']['result']) > 0) {
            $memoryGb = round(((float) $memoryAvailable['data']['data']['result'][0]['value'][1]) / 1024 / 1024 / 1024, 2);

            if ($memoryGb <= $memoryThreshold) {
                $this->createAlert(
                    $server->id,
                    'Memoria disponible',
                    $memoryGb,
                    $memoryThreshold,
                    'warning',
                    'La memoria disponible se encuentra bajo el umbral definido.'
                );
            }

            $this->info("Memoria disponible revisada: {$memoryGb} GB");
        }

        if ($diskAvailable['success'] && count($diskAvailable['data']['data']['result']) > 0) {
            $diskGb = round(((float) $diskAvailable['data']['data']['result'][0]['value'][1]) / 1024 / 1024 / 1024, 2);

            if ($diskGb <= $diskThreshold) {
                $this->createAlert(
                    $server->id,
                    'Disco disponible',
                    $diskGb,
                    $diskThreshold,
                    'warning',
                    'El espacio disponible en disco se encuentra bajo el umbral definido.'
                );
            }

            $this->info("Disco disponible revisado: {$diskGb} GB");
        }

        $this->info('Revisión de métricas finalizada.');

        return Command::SUCCESS;
    }

    private function createAlert($serverId, $metricName, $metricValue, $thresholdValue, $severity, $message)
    {
        $existingAlert = Alert::where('server_id', $serverId)
            ->where('metric_name', $metricName)
            ->where('status', 'open')
            ->first();

        if ($existingAlert) {
            $this->warn("Ya existe una alerta abierta para {$metricName}. No se duplicó el registro.");
            return;
        }

        Alert::create([
            'server_id' => $serverId,
            'metric_name' => $metricName,
            'metric_value' => $metricValue,
            'threshold_value' => $thresholdValue,
            'severity' => $severity,
            'message' => $message,
            'status' => 'open'
        ]);

        $this->warn("Alerta registrada: {$metricName}");
    }
}
