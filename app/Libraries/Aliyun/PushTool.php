<?php

namespace App\Libraries\Aliyun;

require_once __DIR__ . '/../../../vendor/aliyuncs/aliyun-php-sdk-core/Config.php';

use Push\Request\V20160801 as Push;

class PushTool
{
    /**
     * @var self
     */
    private static $_instance;

    /**
     * @var \DefaultAcsClient
     */
    private $client;

    private $accessKeyId;
    private $accessKeySecret;

    private function __construct()
    {
    }

    private function __clone()
    {

    }

    private static function create()
    {
        if (self::$_instance) {
            return self::$_instance;
        }

        $instance = new self();
        $instance->accessKeyId = config('site.aliPush.accessKeyId');
        $instance->accessKeySecret = config('site.aliPush.accessKeySecret');
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $instance->accessKeyId, $instance->accessKeySecret);
        $instance->client = new \DefaultAcsClient($iClientProfile);

        self::$_instance = $instance;
        return $instance;
    }

    public static function instance()
    {
        if (empty(self::$_instance)) {
            self::create();
        }
        return self::$_instance;
    }

    /**
     * @param mixed $request
     * @param string $body
     * @param string $target
     *      DEVICE:根据设备推送
     *      ACCOUNT:根据账号推送
     *      ALIAS:根据别名推送
     *      TAG:根据标签推送
     *      ALL:推送给全部设备
     * @param string $targetValue
     *      Target=DEVICE，值如deviceid111,deviceid1111
     *      Target=ACCOUNT，值如account111,account222
     *      Target=ALIAS，值如alias111,alias222
     *      Target=TAG，支持单Tag和多Tag，格式请参考 标签格式
     *      Target=ALL，值为all
     * @param null | string $title
     * @return mixed
     */
    private function setPushRequest(&$request, $body, $target, $targetValue, $title = null)
    {
        $request->setAppKey(config('site.aliPush.appKey'));
        $request->setTarget($target);
        $request->setTargetValue($targetValue);
        $request->setBody($body);
        if ($title) {
            $request->setTitle($title);
        }
        return $request;
    }

    public function pushNoticeToAndroid($title, $body, $target = 'ALL', $targetValue = 'ALL')
    {
        try {
            $request = new Push\PushNoticeToAndroidRequest();
            $this->setPushRequest($request, $body, $target, $targetValue, $title);

            $response = $this->client->getAcsResponse($request);
            return array('status' => 1, 'data' => $response);
        } catch (\ClientException $e) {
            return array('status' => 0, 'data' => array('type' => $e->getErrorType(), 'code' => $e->getErrorCode(), 'message' => $e->getErrorMessage()));
        }
    }

    public function pushMessageToAndroid($title, $body, $target = 'ALL', $targetValue = 'ALL')
    {
        try {
            $request = new Push\PushMessageToAndroidRequest();
            $this->setPushRequest($request, $body, $target, $targetValue, $title);

            $response = $this->client->getAcsResponse($request);
            return array('status' => 1, 'data' => $response);
        } catch (\ClientException $e) {
            return array('status' => 0, 'data' => array('type' => $e->getErrorType(), 'code' => $e->getErrorCode(), 'message' => $e->getErrorMessage()));
        }
    }

    public function pushNoticeToiOS($body, $target = 'ALL', $targetValue = 'ALL')
    {
        try {
            $request = new Push\PushNoticeToiOSRequest();
            $request->setApnsEnv("DEV");
            $this->setPushRequest($request, $body, $target, $targetValue);

            $response = $this->client->getAcsResponse($request);
            return array('status' => 1, 'data' => $response);
        } catch (\ClientException $e) {
            return array('status' => 0, 'data' => array('type' => $e->getErrorType(), 'code' => $e->getErrorCode(), 'message' => $e->getErrorMessage()));
        }
    }

    public function pushMessageToiOS($title, $body, $target = 'ALL', $targetValue = 'ALL')
    {
        try {
            $request = new Push\PushMessageToiOSRequest();
            $this->setPushRequest($request, $body, $target, $targetValue, $title);

            $response = $this->client->getAcsResponse($request);
            return array('status' => 1, 'data' => $response);
        } catch (\ClientException $e) {
            return array('status' => 0, 'data' => array('type' => $e->getErrorType(), 'code' => $e->getErrorCode(), 'message' => $e->getErrorMessage()));
        }
    }

    /**
     * @param string $deviceType iOS：iOS设备 ANDROID：Andriod设备 ALL：全部类型设备
     * @param string $pushType MESSAGE：表示消息 NOTICE：表示通知
     * @param string $title
     * @param string $body
     * @param string $target
     * @param string $targetValue
     * @param callable $extraConfig
     * @return array
     */
    public function push($deviceType, $pushType, $title, $body, $target = 'ALL', $targetValue = 'ALL', callable $extraConfig)
    {
        try {
            $request = new Push\PushRequest();
            $request->setDeviceType($deviceType);
            $request->setPushType($pushType);
            $this->setPushRequest($request, $body, $target, $targetValue, $title);
            $pushTime = gmdate('Y-m-d\TH:i:s\Z', strtotime('+3 second')); //延迟3秒发送
            $request->setPushTime($pushTime);

            if (is_callable($extraConfig)) {
                $extraConfig($request);
            }

            $response = $this->client->getAcsResponse($request);
            return array('status' => 1, 'data' => $response);
        } catch (\ClientException $e) {
            return array('status' => 0, 'data' => array('type' => $e->getErrorType(), 'code' => $e->getErrorCode(), 'message' => $e->getErrorMessage()));
        }
    }
}