<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Site;
use App\Models\SiteMetric;

class CheckSiteMetrics extends Command
{
    protected $signature = 'site-metrics:check';

    protected $description = 'Revisa métricas individuales por sitio: estado HTTP, tiempo de respuesta, uso de disco y disponibilidad.';

    public function handle()
    {
        $this->info('Iniciando revisión de métricas por sitio...');

        $sites = Site::where('status', 'active')->get();

        if ($sites->count() === 0) {
            $this->warn('No existen sitios activos registrados.');
            return Command::SUCCESS;
        }

        foreach ($sites as $site) {
            $this->info("Revisando sitio: {$site->name}");

            $httpStatus = null;
            $responseTimeMs = null;
            $availabilityStatus = 'unknown';

            if (!empty($site->url)) {
                $start = microtime(true);

                try {
                    $response = Http::timeout(10)->get($site->url);

                    $end = microtime(true);

                    $httpStatus = $response->status();
                    $responseTimeMs = round(($end - $start) * 1000, 2);

                    $availabilityStatus = $response->successful()
                        ? 'available'
                        : 'unavailable';

                } catch (\Exception $e) {
                    $end = microtime(true);

                    $responseTimeMs = round(($end - $start) * 1000, 2);
                    $availabilityStatus = 'unavailable';

                    $this->error("Error HTTP en {$site->name}: " . $e->getMessage());
                }
            }

            $diskUsageMb = $this->getDiskUsageMb($site->document_root);

            SiteMetric::create([
                'site_id' => $site->id,
                'http_status' => $httpStatus,
                'response_time_ms' => $responseTimeMs,
                'disk_usage_mb' => $diskUsageMb,
                'availability_status' => $availabilityStatus
            ]);

            $this->info("HTTP: " . ($httpStatus ?? 'N/D'));
            $this->info("Tiempo respuesta: " . ($responseTimeMs ?? 'N/D') . " ms");
            $this->info("Uso disco: " . ($diskUsageMb ?? 'N/D') . " MB");
            $this->info("Disponibilidad: {$availabilityStatus}");
            $this->line('-----------------------------------');
        }

        $this->info('Revisión de métricas por sitio finalizada.');

        return Command::SUCCESS;
    }

    private function getDiskUsageMb(?string $path): ?float
    {
        if (empty($path) || !is_dir($path)) {
            return null;
        }

        $output = [];
        $returnCode = 0;

        exec('du -sm ' . escapeshellarg($path) . ' 2>/dev/null', $output, $returnCode);

        if ($returnCode !== 0 || empty($output)) {
            return null;
        }

        $parts = preg_split('/\s+/', trim($output[0]));

        return isset($parts[0]) ? (float) $parts[0] : null;
    }
}
