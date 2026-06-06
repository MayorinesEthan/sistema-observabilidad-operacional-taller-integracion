<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table = 'alerts';

    public $timestamps = false;

    protected $fillable = [
        'server_id',
        'metric_name',
        'metric_value',
        'threshold_value',
        'severity',
        'message',
        'status',
        'created_at'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function incident()
    {
        return $this->hasOne(Incident::class, 'alert_id');
    }
}
