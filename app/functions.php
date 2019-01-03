<?php


/**
 * 获取完整URL
 *
 * @param $url
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function get_url($url)
{
    if (empty($url)) {
        return '';
    } else {
        return url($url);
    }
}

/**
 * 获取图片URL
 *
 * @param $url
 * @param $disk null|string
 * @return string
 */
function get_file_url($url, $disk = null)
{
    if (empty($url)) {
        return Storage::disk($disk)->url('404.jpg');
    } elseif (substr_compare(strtolower($url), 'http', 0, 4) == 0) {
        return $url;
    } else {
        return Storage::disk($disk)->url($url);
    }
}

/**
 * HTTP GET
 *
 * @param $url
 * @param array $opts
 * @return string
 */
function curl_get($url, $opts = [])
{
    //初始化
    $ch = curl_init();

    //设置选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);

    foreach ($opts as $k => $opt) {
        curl_setopt($ch, $k, $opt);
    }

    //执行并获取内容
    $result = curl_exec($ch);

    if (!$result) {
        var_dump(curl_error($ch));
    }

    //释放curl句柄
    curl_close($ch);

    return $result;
}

/**
 * HTTP POST
 *
 * @param $url
 * @param $data
 * @return string
 */
function curl_post($url, $data)
{
    //初始化
    $ch = curl_init();

    //设置选项
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    //执行并获取内容
    $result = curl_exec($ch);

    //释放curl句柄
    curl_close($ch);

    return $result;
}


/**
 * 从缓存或回调函数中获取值（正式环境）
 * 从回调函数中获取值（开发环境）
 *
 * @param $key
 * @param $minutes
 * @param $callback
 * @return mixed
 */
function cache_remember($key, $minutes, $callback)
{
    if (env('APP_DEBUG')) {
        return call_user_func($callback);
    } else {
        return Cache::remember($key, $minutes, $callback);
    }
}

/**
 * 计算当前时间
 *
 * @return string
 */
function getmicrotime()
{
    list ($usec, $sec) = explode(" ", microtime());
    return (( float )$usec + ( float )$sec);
}

/**
 * 获取客户端真实IP
 *
 * @return string
 */
function get_client_ip()
{
    return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : Request::getClientIp();
}

/**
 * 数组转对象
 *
 * @param $array
 * @return StdClass
 */
function array_to_object($array)
{
    if (is_array($array)) {
        $obj = new StdClass();
        foreach ($array as $key => $val) {
            $obj->$key = $val;
        }
    } else {
        $obj = $array;
    }
    return $obj;
}

/**
 * 判断当前访问的用户是PC端，还是手机端，返回true为手机端，false为PC端
 * @return boolean
 */
function is_mobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        // 找不到为false,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
            return true;
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 获取主题资源路径
 *
 * @param $path
 * @return string
 */
function theme_asset_path($path)
{
    return public_path('themes' . DIRECTORY_SEPARATOR . $path);
}

/**
 * 获取主题视图路径
 *
 * @param $path
 * @return string
 */
function theme_view_path($path)
{
    return resource_path('views/themes' . DIRECTORY_SEPARATOR . $path);
}

/**
 * 获取浏览器信息
 *
 * @return string
 */
function get_ua_browser()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $browser = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/MSIE/i', $browser)) {
            $browser = 'IE';
        } elseif (preg_match('/Firefox/i', $browser)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Chrome/i', $browser)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $browser)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge/i', $browser)) {
            $browser = 'Edge';
        } else {
            $browser = 'Other';
        }
        return $browser;
    } else {
        return '';
    }
}

/**
 * 获取操作系统信息
 *
 * @return string
 */
function get_ua_os()
{
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $OS = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/win/i', $OS)) {
            $OS = 'Windows';
        } elseif (preg_match('/mac/i', $OS)) {
            $OS = 'Mac';
        } elseif (preg_match('/linux/i', $OS)) {
            $OS = 'Linux';
        } elseif (preg_match('/unix/i', $OS)) {
            $OS = 'Unix';
        } elseif (preg_match('/bsd/i', $OS)) {
            $OS = 'BSD';
        } elseif (preg_match('/android/i', $OS)) {
            $OS = 'Android';
        } elseif (preg_match('/iphone/i', $OS)) {
            $OS = 'iOS';
        } else {
            $OS = 'Other';
        }
        return $OS;
    } else {
        return "获取访客操作系统信息失败！";
    }
}

/**
 * 根据生日计算年龄
 *
 * @param $birthday
 * @return string
 */
function calculateAge($birthday)
{
    $age = strtotime($birthday);
    if ($age === false) {
        return false;
    }
    list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
    $now = strtotime("now");
    list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
    $age = $y2 - $y1;
    if ((int)($m2 . $d2) < (int)($m1 . $d1))
        $age -= 1;
    return $age;
}
