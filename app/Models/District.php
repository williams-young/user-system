<?php

namespace App\Models;

class District extends BaseModel
{

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getFullName()
    {
        return $this->name . $this->extra . $this->suffix;
    }
}