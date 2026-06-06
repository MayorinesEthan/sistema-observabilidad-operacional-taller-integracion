<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Incident;
use App\Models\Report;
use App\Models\Client;
use App\Models\Site;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function operational()
    {
        $now = Carbon::now('America/Santiago');

        $totalClients = Client::count();
        $totalSites = Site::count();

        $totalAlerts = Alert::count();
        $openAlerts = Alert::where('status', 'open')->count();
        $closedAlerts = Alert::where('status', 'closed')->count();

        $totalIncidents = Incident::count();
        $openIncidents = Incident::where('status', 'open')->count();
        $closedIncidents = Incident::where('status', 'closed')->count();

        $criticalIncidents = Incident::where('priority', 'critical')->count();
        $highIncidents = Incident::where('priority', 'high')->count();
        $mediumIncidents = Incident::where('priority', 'medium')->count();
        $lowIncidents = Incident::where('priority', 'low')->count();

        $avgResolutionTime = Incident::whereNotNull('resolution_time_minutes')
            ->avg('resolution_time_minutes');

        $recentIncidents = Incident::with(['alert.server.sites.client', 'logs'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentAlerts = Alert::with('server.sites.client')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $report = Report::create([
            'title' => 'Reporte operacional - ' . $now->format('Y-m-d H:i'),
            'report_type' => 'operational',
            'file_path' => null,
            'sent_by_email' => false
        ]);

        return view('reports.operational', compact(
            'now',
            'report',
            'totalClients',
            'totalSites',
            'totalAlerts',
            'openAlerts',
            'closedAlerts',
            'totalIncidents',
            'openIncidents',
            'closedIncidents',
            'criticalIncidents',
            'highIncidents',
            'mediumIncidents',
            'lowIncidents',
            'avgResolutionTime',
            'recentIncidents',
            'recentAlerts'
        ));
    }
}
