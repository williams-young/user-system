<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;

    const STATES = [];
    const TYPES = [];

    protected $fillable = [];

    public function typeName()
    {
        return array_key_exists($this->type, static::TYPES) ? static::TYPES[$this->type] : '';
    }

    public function stateName()
    {
        return array_key_exists($this->state, static::STATES) ? static::STATES[$this->state] : '';
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function userLogs()
    {
        return $this->morphMany(UserLog::class,'refer');
    }
}