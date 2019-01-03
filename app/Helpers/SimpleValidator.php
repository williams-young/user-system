<?php

namespace App\Helpers;

/**
 * 一个简单的验证类
 */
class SimpleValidator
{
    public static function email($value)
    {
        $value = (string) $value;
        $valid = filter_var($value, FILTER_VALIDATE_EMAIL);
        return $valid !== false ;
    }

    public static function username($username)
    {
        return !!preg_match('/[A-Za-z0-9_\-\x{4e00}-\x{9fa5}]{2,32}$/u', $username);
    }

    public static function nickname($value, array $option = array())
    {
        
        $option = array_merge(
            array('minLength' => 3, 'maxLength' => 20),
            $option
        );

        $len = (strlen($value) + mb_strlen($value, 'utf-8')) / 2;
        if ($len > $option['maxLength'] || $len < $option['minLength']) {
            return false;
        }
        return !!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-z0-9_.]+$/u', $value);
    }

    public static function password($value)
    {
        return !!preg_match('/^[\da-zA-Z@#!$%^&-~_]{5,20}$/u', $value);
    }

    public static function idcard($value)
    {
        return !!preg_match('/^\d{17}[0-9xX]$/', $value);
    }

    public static function bankCardId($value)
    {
        return !!preg_match('/^(\d{16}|\d{19})$/', $value);
    }

    public static function mobile($value)
    {
        return !!preg_match('/^0?(13|14|15|17|18)[0-9]{9}$/', $value);
    }

    public static function phone($value)
    {
        return !!preg_match('/^(\d{4}-|\d{3}-)?(\d{8}|\d{7})$/', $value);
    }

    public static function date($value)
    {
        return !!preg_match('/^(\d{4}|\d{2})-((0?([1-9]))|(1[0-2]))-((0?[1-9])|([12]([0-9]))|(3[0|1]))$/', $value);
    }

    public static function qq($value)
    {
        return !!preg_match('/^[1-9]\d{4,}$/', $value);
    }

    public static function integer($value)
    {
        return !!preg_match('/^[+-]?\d{1,9}$/', $value);
    }

    public static function naturalNumber($value)
    {
        return !!preg_match('/^(0|([1-9]\d*))$/', $value);
    }

    public static function positiveInteger($value)
    {
        return !!preg_match('/^[1-9]\d{0,11}$/', $value);
    }

    public static function float($value)
    {
        return !!preg_match('/^(([+-]?[1-9]{1}\d*)|([+-]?[0]{1}))(\.(\d){1,2})?$/i', $value);
    }

    public static function dateTime($value)
    {
        return !!preg_match('/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/', $value);
    }

    public static function site($value)
    {
        return !!preg_match('/^(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/', $value);
    }

    public static function chineseAndAlphanumeric($value)
    {
        return !!preg_match('/^([\x{4e00}-\x{9fa5}]|[a-zA-Z0-9_.·])*$/u', $value);
    }

    public static function ip($value)
    {
        return !!preg_match('/^(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)\.(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)\.(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)\.(25[0-5]|2[0-4]\d|[0-1]\d{2}|[1-9]?\d)$/', $value);
    }
}