<?php
/**
 * Author: zhangjn
 * Date: 2018/5/9
 * Time: 18:03
 */

require('CurlHelper.php');

class Demo1
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

//创建微信卡券模板
$post_data = [
    'app_id'=>Demo1::$wxappid,
    'full_name'=>'深圳盛灿科技股份有限公司',
    'short_name'=>'盛灿科技',
];
$url = $host . '/card/create-template?signature='.Demo1::generate_sign($post_data, $timestamp).'&timestamp='.$timestamp.'&sn='.$timestamp.$timestamp.'&appkey='.Demo1::$appkey;
echo $url.'<br />';

// 接口/card/create-template返回的card_id


//发票开具(无用户抬头)
/*$post_data = [
    'app_id'=>Demo1::$wxappid,
    'order_id'=>"$timestamp",
    'money'=>0.01,
    'timestamp'=>$timestamp,
    'type'=>1,
    'source'=>'web',
    "callback_url"=>"http://www.snsshop.com",
    "taxpayer_num"=>"9144030007437956X1",
    "goods_info"=>[
        [
            "name"=>"商品1",
            "tax_rate"=>"0.13",
            "models"=>"XYZ",
            "unit"=>"个",
            "total_price"=>"10",
            "tax_amount"=>"1.3",
            "total"=>2,
            "include_tax_flag"=>"1",
            "tax_code"=>"1020202000000000000"
        ]
    ],
    "card_id"=>"plN5twTaq6HqL3UOH0QQc8lSJm-o"
];
$url = $host . '/authorize/collect-invoice-title?signature='.Demo1::generate_sign($post_data, $timestamp).'&timestamp='.$timestamp.'&sn='.$timestamp.$timestamp.'&appkey='.Demo1::$appkey;*/

$res = CurlHelper::http_post($url, $post_data, 'json');
var_dump($res);
echo '<br />';
$res = json_decode($res,true);
var_dump($res);
echo '<br />';