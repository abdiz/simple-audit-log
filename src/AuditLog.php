<?php

namespace AbdiZbn\SimpleAuditLog;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $dates = ['created_at'];

    protected $fillable = [
        'old_values',
        'new_values',
        'event',
        'module',
        'module_id',

        'user_id',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
