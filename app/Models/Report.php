<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'report_type',
        'file_path',
        'sent_by_email',
        'created_at'
    ];
}
