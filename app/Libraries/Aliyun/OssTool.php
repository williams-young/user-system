<?php

namespace App\Libraries\Aliyun;

require_once __DIR__ . '/../../../vendor/aliyuncs/aliyun-php-sdk-core/Config.php';

use Sts\Request\V20150401 as Sts;

class OssTool
{
    private function read_file($fname)
    {
        $content = '';
        if (!file_exists($fname)) {
            throw new \Exception("文件" . basename($fname) . "不存在");
        }
        $handle = fopen($fname, "rb");
        while (!feof($handle)) {
            $content .= fread($handle, 10000);
        }
        fclose($handle);
        return $content;
    }

    public function getToken()
    {
        $content = $this->read_file(__DIR__ . '/Oss/config.json');
        $myjsonarray = json_decode($content);

        $accessKeyID = $myjsonarray->AccessKeyID;
        $accessKeySecret = $myjsonarray->AccessKeySecret;
        $roleArn = $myjsonarray->RoleArn;
        $tokenExpire = $myjsonarray->TokenExpireTime;
        $policy = $this->read_file(__DIR__ . '/Oss/' . $myjsonarray->PolicyFile);

        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", $accessKeyID, $accessKeySecret);
        $client = new \DefaultAcsClient($iClientProfile);

        $request = new Sts\AssumeRoleRequest();
        $request->setRoleSessionName("client_name");
        $request->setRoleArn($roleArn);
        $request->setPolicy($policy);
        $request->setDurationSeconds($tokenExpire);
        $response = $client->doAction($request);

        $rows = array();
        $body = $response->getBody();
        $content = json_decode($body);
        $rows['status'] = $response->getStatus();
        if ($response->getStatus() == 200) {
            $rows['AccessKeyId'] = $content->Credentials->AccessKeyId;
            $rows['AccessKeySecret'] = $content->Credentials->AccessKeySecret;
            $rows['Expiration'] = $content->Credentials->Expiration;
            $rows['SecurityToken'] = $content->Credentials->SecurityToken;
        } else {
            $rows['code'] = $content->Code;
            $rows['message'] = $content->Message;
        }

        return $rows;
    }
}

?>
