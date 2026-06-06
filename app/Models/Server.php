<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $table = 'servers';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'ip_address',
        'description',
        'status',
        'created_at'
    ];

    public function sites()
    {
        return $this->hasMany(Site::class, 'server_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'server_id');
    }

    public function metricsLogs()
    {
        return $this->hasMany(MetricsLog::class, 'server_id');
    }
}
