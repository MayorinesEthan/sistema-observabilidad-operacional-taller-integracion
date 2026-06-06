<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentLog;
use Illuminate\Http\Request;

class IncidentLogController extends Controller
{
    public function store(Request $request, $incidentId)
    {
        $request->validate([
            'action' => 'required|string|max:150',
            'comment' => 'nullable|string'
        ]);

        $incident = Incident::findOrFail($incidentId);

        IncidentLog::create([
            'incident_id' => $incident->id,
            'action' => $request->action,
            'comment' => $request->comment
        ]);

        return redirect('/dashboard')->with('message', 'Acción registrada en la bitácora del incidente.');
    }
}
