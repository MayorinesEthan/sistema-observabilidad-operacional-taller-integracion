<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Server;
use App\Models\Site;
use App\Models\Alert;
use App\Models\Incident;
use App\Models\Report;
use App\Models\User;
use App\Services\PrometheusService;

class DashboardController extends Controller
{
    public function index(PrometheusService $prometheus)
    {
        $totalServers = Server::count();
        $totalSites = Site::count();
        $totalClients = Client::count();
        $highPriorityClients = Client::where('priority_level', 'high')->count();

        $openAlerts = Alert::where('status', 'open')->count();
        $openIncidents = Incident::where('status', 'open')->count();
        $totalReports = Report::count();

        $recentAlerts = Alert::with('server.sites.client')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $sitesWithMetrics = Site::with(['client', 'server', 'latestMetric'])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $teamUsers = User::orderBy('name')->get();

        $serverStatus = $prometheus->getServerStatus();
        $cpuUsage = $prometheus->getCpuUsage();
        $memoryAvailable = $prometheus->getMemoryAvailable();
        $diskAvailable = $prometheus->getDiskAvailable();

        return view('dashboard', compact(
            'totalServers',
            'totalSites',
            'totalClients',
            'highPriorityClients',
            'openAlerts',
            'openIncidents',
            'totalReports',
            'recentAlerts',
            'sitesWithMetrics',
            'teamUsers',
            'serverStatus',
            'cpuUsage',
            'memoryAvailable',
            'diskAvailable'
        ));
    }
}
