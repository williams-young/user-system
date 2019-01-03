<?php

namespace App\Models;

class PermissionRole extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'permission_id',
        'role_id'
    ];

}