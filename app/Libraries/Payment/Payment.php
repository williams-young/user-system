<?php
/**
 * Created by PhpStorm.
 * User: ebsinori
 * Date: 2017/6/7
 * Time: 下午3:12
 */

namespace App\Libraries\Payment;


interface Payment
{
    public function unifiedOrder($data);

    public function orderQuery($orderSn);

    public function closeOrder($orderSn);

}