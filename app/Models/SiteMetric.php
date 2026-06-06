<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteMetric extends Model
{
    protected $table = 'site_metrics';

    public $timestamps = false;

    protected $fillable = [
        'site_id',
        'http_status',
        'response_time_ms',
        'disk_usage_mb',
        'availability_status',
        'checked_at'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}
