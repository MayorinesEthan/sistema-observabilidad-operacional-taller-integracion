<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetricsLog extends Model
{
    protected $table = 'metrics_logs';

    public $timestamps = false;

    protected $fillable = [
        'server_id',
        'metric_name',
        'metric_value',
        'unit',
        'recorded_at'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }
}
