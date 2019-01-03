<?php

namespace App\Models;

class Setting extends BaseModel
{
    protected $fillable = [
        'name',
        'value'
    ];

    public static function getValue($name, $default = null)
    {
        $option = Setting::where('name', $name)->first();
        if ($option) {
            return $option->value;
        } else {
            return $default;
        }
    }

    public static function set($name, $value)
    {
        $option = Setting::where('name', $name)->first();
        if ($option) {
            $option->value = $value;
            $option->save();
        } else {
            static::create(['name' => $name, 'value' => $value]);
        }
    }

}
