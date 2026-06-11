<?php

namespace Tests\Unit;

use App\Models\Alert;
use App\Models\Client;
use App\Models\Server;
use App\Models\Site;
use App\Services\IncidentPriorityService;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class IncidentPriorityServiceTest extends TestCase
{
    public function test_high_priority_client_with_critical_alert_returns_critical_priority(): void
    {
        $alert = $this->makeAlertWithClientPriority(
            clientPriority: 'high',
            metricName: 'CPU',
            severity: 'critical'
        );

        $service = new IncidentPriorityService();

        $this->assertEquals('critical', $service->calculate($alert));
    }

    public function test_high_priority_client_with_warning_alert_returns_high_priority(): void
    {
        $alert = $this->makeAlertWithClientPriority(
            clientPriority: 'high',
            metricName: 'CPU',
            severity: 'warning'
        );

        $service = new IncidentPriorityService();

        $this->assertEquals('high', $service->calculate($alert));
    }

    public function test_medium_priority_client_with_warning_alert_returns_medium_priority(): void
    {
        $alert = $this->makeAlertWithClientPriority(
            clientPriority: 'medium',
            metricName: 'CPU',
            severity: 'warning'
        );

        $service = new IncidentPriorityService();

        $this->assertEquals('medium', $service->calculate($alert));
    }

    public function test_critical_alert_without_client_context_returns_high_priority(): void
    {
        $alert = new Alert([
            'metric_name' => 'CPU',
            'severity' => 'critical',
        ]);

        $service = new IncidentPriorityService();

        $this->assertEquals('high', $service->calculate($alert));
    }

    public function test_cpu_alert_returns_cpu_recommended_action(): void
    {
        $alert = new Alert([
            'metric_name' => 'CPU',
            'severity' => 'warning',
        ]);

        $service = new IncidentPriorityService();

        $this->assertStringContainsString(
            'Revisar procesos activos',
            $service->recommendedAction($alert)
        );
    }

    public function test_disk_alert_returns_disk_recommended_action(): void
    {
        $alert = new Alert([
            'metric_name' => 'Disco',
            'severity' => 'warning',
        ]);

        $service = new IncidentPriorityService();

        $this->assertStringContainsString(
            'Revisar espacio disponible',
            $service->recommendedAction($alert)
        );
    }

    private function makeAlertWithClientPriority(
        string $clientPriority,
        string $metricName,
        string $severity
    ): Alert {
        $client = new Client([
            'name' => 'Cliente Demo',
            'priority_level' => $clientPriority,
        ]);

        $site = new Site([
            'name' => 'Sitio Demo',
            'domain' => 'demo.local',
        ]);

        $site->setRelation('client', $client);

        $server = new Server([
            'name' => 'Servidor Demo',
            'ip_address' => '127.0.0.1',
        ]);

        $server->setRelation('sites', new Collection([$site]));

        $alert = new Alert([
            'metric_name' => $metricName,
            'severity' => $severity,
        ]);

        $alert->setRelation('server', $server);

        return $alert;
    }
}
