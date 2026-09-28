<?php

namespace AbdiZbn\SimpleAuditLog\Tests\Fixtures;

use AbdiZbn\SimpleAuditLog\AuditableTrait;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use AuditableTrait;

    protected $guarded = [];

    public function getModule()
    {
        return 'post';
    }

    public function getDontAuditing()
    {
        return ['secret'];
    }
}
