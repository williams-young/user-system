<?php
/**
 * Created by PhpStorm.
 * User: ebsinori
 * Date: 2017/6/8
 * Time: 上午10:38
 */

namespace App\Libraries\Payment;

use App\ArrayToolkit;
use App\Models\Order;

class BasePayment
{
    protected $order = [];

    public function __construct($order = [])
    {
        $this->order = $order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    protected function checkOrderData($data)
    {
        if (!ArrayToolkit::requires($data, ['outTradeNo', 'subject', 'amount', 'notifyUrl'])) {
            throw new \Exception('参数缺失');
        }
        if ($this->order['state'] != Order::STATE_NOPAY) {
            throw new \Exception('该订单无法支付');
        }
    }

}