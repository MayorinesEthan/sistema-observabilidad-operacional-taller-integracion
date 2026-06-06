<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $table = 'sites';

    public $timestamps = false;

    protected $fillable = [
        'server_id',
        'client_id',
        'name',
        'domain',
        'url',
        'document_root',
        'platform',
        'status',
        'created_at'
    ];

    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function metrics()
    {
        return $this->hasMany(SiteMetric::class, 'site_id');
    }

    public function latestMetric()
    {
        return $this->hasOne(SiteMetric::class, 'site_id')->latest('checked_at');
    }
}
