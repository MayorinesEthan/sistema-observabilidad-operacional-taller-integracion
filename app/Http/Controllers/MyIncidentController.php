<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class MyIncidentController extends Controller
{
    public function index()
    {
        $incidents = Incident::with(['alert.server.sites.client', 'logs'])
            ->where('assigned_user_id', Auth::id())
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('incidents.my-incidents', compact('incidents'));
    }
}
