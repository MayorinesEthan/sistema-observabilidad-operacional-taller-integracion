<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'contact_email',
        'priority_level',
        'status',
        'created_at'
    ];

    public function sites()
    {
        return $this->hasMany(Site::class, 'client_id');
    }
}
