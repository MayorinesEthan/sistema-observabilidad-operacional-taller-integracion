<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentLog extends Model
{
    protected $table = 'incident_logs';

    public $timestamps = false;

    protected $fillable = [
        'incident_id',
        'action',
        'comment',
        'created_at'
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }
}
