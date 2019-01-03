<?php

namespace App\Models;

class Role extends BaseModel
{

    protected $fillable = [
        'name',
        'description',
        'type',
        'creator_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
