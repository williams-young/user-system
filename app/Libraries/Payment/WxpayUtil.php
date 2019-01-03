<?php

namespace App\Libraries\Payment;

use App\Models\Order;
use App\StringToolkit;

require_once "wxpay/WxPay.Exception.php";
require_once "wxpay/WxPay.Config.php";
require_once "wxpay/WxPay.Data.php";

class WxpayUtil extends BasePayment implements Payment
{
    private $unifiedOrderUrl = "https://api.mch.weixin.qq.com/sandboxnew/pay/unifiedorder";
    private $orderQueryUrl = 'https://api.mch.weixin.qq.com/sandboxnew/pay/orderquery';
    private $reportUrl = 'https://api.mch.weixin.qq.com/sandboxnew/payitil/report';
    private $closeOrderUrl = 'https://api.mch.weixin.qq.com/sandboxnew/pay/closeorder';
    private $downloadBillUrl = 'https://api.mch.weixin.qq.com/sandboxnew/pay/downloadbill';

    public function __construct($order = [])
    {
        parent::__construct($order);

    }

    /**
     * APP支付下单方法
     * @param $data
     * @return array
     */
    public function unifiedOrder($data)
    {
        $this->checkOrderData($data);
        $input = new \WxPayUnifiedOrder();
        $input->SetOut_trade_no($data['outTradeNo']);
        $input->SetBody($data['subject']);
        $input->SetTotal_fee($data['amount']);
        $input->SetNotify_url($data['notifyUrl']);
        $input->SetTime_start(date('YmdHis', $this->order['createdTime']));
        $input->SetTime_expire(date('YmdHis', strtotime(Order::ORDER_EXPIRED_TIME, $this->order['createdTime'])));

        $input->SetTrade_type('APP');
        $this->setPublicParams($input);
        $result = $this->requestApi($input, $this->unifiedOrderUrl);

        $dataObj = new \WxPayDataBase();
        $dataObj->SetValues([
            'appid' => \WxPayConfig::APPID,
            'partnerid' => \WxPayConfig::MCHID,
            'prepayid' => $result['prepay_id'],
            'package' => 'Sign=WXPay',
            'noncestr' => StringToolkit::createRandomString(32, true),
            'timestamp' => time()
        ]);
        $dataObj->SetSign();

        return ['payment' => 2, 'data' => $dataObj->GetValues()];
    }

    public function orderQuery($orderSn)
    {
        $input = new \WxPayOrderQuery();
        $input->SetOut_trade_no($orderSn);
        $this->setPublicParams($input, false);
        $result = $this->requestApi($input, $this->orderQueryUrl);
        return $result;
    }

    public function closeOrder($orderSn)
    {
        $input = new \WxPayCloseOrder();
        $input->SetOut_trade_no($orderSn);
        $this->setPublicParams($input, false);
        $result = $this->requestApi($input, $this->closeOrderUrl);
        return $result;
    }

    public function downloadBill($date, $type = 'ALL')
    {
        $input = new \WxPayDownloadBill();
        $input->SetBill_date($date);
        $input->SetBill_type($type);
        $this->setPublicParams($input, false);

        $xml = $input->ToXml();
        return $this->postXmlCurl($xml, $this->downloadBillUrl, false, 6);
    }

    public function handleNotification()
    {
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        try {
            $result = \WxPayResults::Init($xml);
            return ['status' => 'success', 'data' => $result];
        } catch (\WxPayException $e) {
            return ['status' => 'failure', 'message' => $e->errorMessage()];
        }
    }

    public function notifyReply($code = 'SUCCESS', $msg = 'OK')
    {
        $notifyObj = new \WxPayNotifyReply();
        $notifyObj->SetReturn_code($code);
        $notifyObj->SetReturn_msg($msg);
        return $notifyObj->ToXml();
    }

    /**
     * @param $input mixed
     * @param $setIp
     */
    private function setPublicParams(&$input, $setIp = true)
    {
        $input->SetAppid(\WxPayConfig::APPID);//公众账号ID
        $input->SetMch_id(\WxPayConfig::MCHID);//商户号
        if ($setIp) {
            $input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);//终端ip
        }
        $input->SetNonce_str(StringToolkit::createRandomString(32, true));//随机字符串
        $input->SetSign();//签名
    }

    /**
     * @param $input mixed
     * @param $url string
     * @param $timeOut int
     * @return array
     */
    private function requestApi($input, $url, $timeOut = 6)
    {
        $xml = $input->ToXml();
        $startTimeStamp = $this->getMillisecond();//请求开始时间
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = \WxPayResults::Init($response);
        $this->reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        return $result;
    }

    private function getMillisecond()
    {
        //获取毫秒的时间戳
        $time = explode(" ", microtime());
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode(".", $time);
        $time = $time2[0];
        return $time;
    }

    private function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);

        //如果有配置代理这里就设置代理
        if (\WxPayConfig::CURL_PROXY_HOST != "0.0.0.0"
            && \WxPayConfig::CURL_PROXY_PORT != 0) {
            curl_setopt($ch, CURLOPT_PROXY, \WxPayConfig::CURL_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, \WxPayConfig::CURL_PROXY_PORT);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if ($useCert == true) {
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLCERT, \WxPayConfig::SSLCERT_PATH);
            curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($ch, CURLOPT_SSLKEY, \WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            throw new \WxPayException("curl出错，错误码:$error");
        }
    }

    private function reportCostTime($url, $startTimeStamp, $data)
    {
        //如果不需要上报数据
        if (\WxPayConfig::REPORT_LEVENL == 0) {
            return;
        }
        //如果仅失败上报
        if (\WxPayConfig::REPORT_LEVENL == 1 &&
            array_key_exists("return_code", $data) &&
            $data["return_code"] == "SUCCESS" &&
            array_key_exists("result_code", $data) &&
            $data["result_code"] == "SUCCESS") {
            return;
        }

        //上报逻辑
        $endTimeStamp = $this->getMillisecond();
        $objInput = new \WxPayReport();
        $objInput->SetInterface_url($url);
        $objInput->SetExecute_time_($endTimeStamp - $startTimeStamp);
        //返回状态码
        if (array_key_exists("return_code", $data)) {
            $objInput->SetReturn_code($data["return_code"]);
        }
        //返回信息
        if (array_key_exists("return_msg", $data)) {
            $objInput->SetReturn_msg($data["return_msg"]);
        }
        //业务结果
        if (array_key_exists("result_code", $data)) {
            $objInput->SetResult_code($data["result_code"]);
        }
        //错误代码
        if (array_key_exists("err_code", $data)) {
            $objInput->SetErr_code($data["err_code"]);
        }
        //错误代码描述
        if (array_key_exists("err_code_des", $data)) {
            $objInput->SetErr_code_des($data["err_code_des"]);
        }
        //商户订单号
        if (array_key_exists("out_trade_no", $data)) {
            $objInput->SetOut_trade_no($data["out_trade_no"]);
        }
        //设备号
        if (array_key_exists("device_info", $data)) {
            $objInput->SetDevice_info($data["device_info"]);
        }

        try {
            $this->report($objInput);
        } catch (\WxPayException $e) {
            //不做任何处理
        }
    }

    private function report(\WxPayReport $inputObj, $timeOut = 1)
    {
        //检测必填参数
        if (!$inputObj->IsInterface_urlSet()) {
            throw new \WxPayException("接口URL，缺少必填参数interface_url！");
        }
        if (!$inputObj->IsReturn_codeSet()) {
            throw new \WxPayException("返回状态码，缺少必填参数return_code！");
        }
        if (!$inputObj->IsResult_codeSet()) {
            throw new \WxPayException("业务结果，缺少必填参数result_code！");
        }
        if (!$inputObj->IsUser_ipSet()) {
            throw new \WxPayException("访问接口IP，缺少必填参数user_ip！");
        }
        if (!$inputObj->IsExecute_time_Set()) {
            throw new \WxPayException("接口耗时，缺少必填参数execute_time_！");
        }

        $inputObj->SetTime(date("YmdHis"));//商户上报时间
        $this->setPublicParams($inputObj);
        $xml = $inputObj->ToXml();

        $response = $this->postXmlCurl($xml, $this->reportUrl, false, $timeOut);
        return $response;
    }

}