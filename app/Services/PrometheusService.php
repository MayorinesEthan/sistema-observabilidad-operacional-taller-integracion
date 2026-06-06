<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PrometheusService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('PROMETHEUS_URL', 'http://localhost:9090'), '/');
    }

    public function query(string $promql): array
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/api/v1/query', [
                'query' => $promql
            ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Prometheus no respondió correctamente.',
                    'data' => null
                ];
            }

            return [
                'success' => true,
                'error' => null,
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    public function getServerStatus(): array
    {
        return $this->query('up');
    }

    public function getMemoryAvailable(): array
    {
        return $this->query('node_memory_MemAvailable_bytes');
    }

    public function getCpuUsage(): array
    {
        return $this->query('100 - (avg by(instance) (rate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)');
    }

    public function getDiskAvailable(): array
    {
        return $this->query('node_filesystem_avail_bytes{mountpoint="/"}');
    }
}
