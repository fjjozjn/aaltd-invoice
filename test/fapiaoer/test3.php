<?php
/**
 * Author: zhangjn
 * Date: 2018/5/29
 * Time: 18:07
 */

require('CurlHelper.php');

class Demo3
{
    public static $wxappid = 'wx4e9ee8a65ee30378';
    public static $appkey = '2018050956beeb50';
    public static $appsecret = 'cZOdH0EOvoyMPJrIVQeNx0GHuuudmJZi';

    //生成带签名数组
    public static function generate_sign($params, $timestamp)
    {
        if (isset($params['sign'])) {
            unset($params['sign']);
        }
        ksort($params);
        print_r($params);
        echo '<br />';
        $content = "";
        foreach ($params as $key => $val) {
            if (is_array($val)) {
                $val = json_encode($val, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
            }
            $content .= "$key" . "=" . "$val" . "&";
        }
        echo $content.'<br />';
        $sign_str = self::$appkey.$timestamp.urlencode(rtrim($content, '&')).self::$appsecret;
        echo $sign_str.'<br />';
        $sign = strtoupper( md5($sign_str) );
        echo $sign.'<br />';
        return $sign;
    }
}


$timestamp = time();
echo $timestamp.'<br />';
$host = 'http://182.254.219.106:8400';



//zlf
$post_data = [
    'mobile'=>"13800138000",
];
$url = $host . '/member/check?signature='.Demo3::generate_sign($post_data, $timestamp).'&timestamp='.$timestamp.'&sn='.$timestamp.$timestamp.'&appkey='.Demo3::$appkey;
echo $url.'<br />';


/*$res = CurlHelper::http_post($url, $post_data, 'json');
var_dump($res);
echo '<br />';
$res = json_decode($res,true);
var_dump($res);
echo '<br />';*/