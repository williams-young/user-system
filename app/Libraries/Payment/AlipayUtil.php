<?php

namespace App\Libraries\Payment;

include_once __DIR__ . '/alipay/AopSdk.php';

class AlipayUtil extends BasePayment implements Payment
{
    /**
     * @var \AopClient
     */
    private $client;

    public function __construct($order = [])
    {
        parent::__construct($order);
        $this->client = new \AopClient ();
        $this->client->appId = config('site.alipay.appId');

        $this->client->gatewayUrl = config('site.alipay.gatewayUrl');
        $this->client->signType = config('site.alipay.signType');

        // 应用私钥
        $this->client->rsaPrivateKey = config('site.alipay.rsaPrivateKey');

        // 支付宝公钥
        $this->client->alipayrsaPublicKey = config('site.alipay.alipayrsaPublicKey');
    }

    /**
     * APP支付下单方法
     * @param $data
     * @return array APP支付请求参数
     */
    public function unifiedOrder($data)
    {
        $this->checkOrderData($data);

        $request = new \AlipayTradeAppPayRequest();
        $request->setNotifyUrl($data['notifyUrl']);

        $bizContent = [
            'product_code' => 'QUICK_MSECURITY_PAY',
            'out_trade_no' => $data['outTradeNo'],
            'subject' => $data['subject'],
            'total_amount' => $data['amount'] / 100
        ];

        if (!empty($data['detail'])) {
            $bizContent['body'] = $data['detail'];
        }
        if (!empty($data['goodsType'])) {
            $bizContent['goods_type'] = $data['goodsType'];
        }
        $bizContent['timeout_express'] = '48h';

        $request->setBizContent(json_encode($bizContent));
        return ['payment' => 1, 'data' => $this->client->sdkExecute($request)];
    }

    public function orderQuery($orderSn)
    {
        $request = new \AlipayTradeQueryRequest();
        $request->setBizContent(json_encode(['out_trade_no' => $orderSn]));
        $result = $this->client->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $response = (array)$result->$responseNode;
        if ($response['code'] != '10000') {
            $msg = empty($response['sub_msg']) ? $response['msg'] : $response['sub_msg'];
            throw new \Exception($msg);
        }
        return $response;
    }

    public function closeOrder($orderSn)
    {
        $request = new \AlipayTradeCloseRequest();
        $request->setBizContent(json_encode(['out_trade_no' => $orderSn]));
        $result = $this->client->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $response = (array)$result->$responseNode;
        if ($response['code'] != '10000') {
            $msg = empty($response['sub_msg']) ? $response['msg'] : $response['sub_msg'];
            throw new \Exception($msg);
        }
        return $response;
    }

    public function downloadBill($date, $type = 'trade')
    {
        $request = new \AlipayDataDataserviceBillDownloadurlQueryRequest();
        $request->setBizContent(json_encode(['bill_type' => $type, 'bill_date' => $date]));
        $result = $this->client->execute($request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $response = (array)$result->$responseNode;
        if ($response['code'] != '10000') {
            $msg = empty($response['sub_msg']) ? $response['msg'] : $response['sub_msg'];
            throw new \Exception($msg);
        }
        return $response['bill_download_url'];
    }

    public function rsaCheck($params)
    {
        return $this->client->rsaCheckV1($params, null, $this->client->signType);
    }

}