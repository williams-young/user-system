<?php

namespace App\Models;

class UserDetail extends BaseModel
{
    protected $fillable = [
        'user_id',
        'province',
        'city',
        'district',
        'birtyday',
        'signature',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDistrictName($type = 'province', $full = false)
    {
        if (empty($this->$type)) {
            return '';
        }
        $district = District::findOrFail($this->$type);
        if ($full) {
            return $district->getFullName();
        } else {
            return $district->name;
        }
    }
}