<?php
/**
 * Created by PhpStorm.
 * User: ebsinori
 * Date: 2017/6/7
 * Time: 下午3:06
 */

namespace App\Libraries\Payment;

class PaymentFactory
{
    public static function create($name, $order)
    {
        $class = __NAMESPACE__ . '\\' . ucfirst($name) . 'Util';
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Unknown payment: %s', $name));
        }
        return new $class($order);
    }
}