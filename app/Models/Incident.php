<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'incidents';

    public $timestamps = false;

    protected $fillable = [
        'alert_id',
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'assigned_user_id',
        'root_cause',
        'action_taken',
        'resolution',
        'created_at',
        'closed_at',
        'resolved_at',
        'resolution_time_minutes'
    ];

    public function alert()
    {
        return $this->belongsTo(Alert::class, 'alert_id');
    }

    public function logs()
    {
        return $this->hasMany(IncidentLog::class, 'incident_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
