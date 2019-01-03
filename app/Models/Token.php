<?php

namespace App\Models;

class Token extends BaseModel
{
    const TYPE_APP = 0;
    const TYPE_WECHAT = 1;

    protected $fillable = [
        'user_id',
        'token',
        'type',
    ];
}
