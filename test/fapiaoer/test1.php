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
                //$val = json_encode($val);
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
/*$post_data = [
    'app_id'=>Demo1::$wxappid,
    'full_name'=>'深圳盛灿科技股份有限公司',
    'short_name'=>'盛灿科技',
];
$url = $host . '/card/create-template?signature='.Demo1::generate_sign($post_data, $timestamp).'&timestamp='.$timestamp.'&sn='.$timestamp.$timestamp.'&appkey='.Demo1::$appkey;
echo $url.'<br />';*/

// 接口/card/create-template返回的card_id
// plN5twRtkBnzwHup_UYoSC5n8c2o

//发票开具(无用户抬头)
/*$post_data = [
    'app_id'=>Demo1::$wxappid,
    'order_id'=>"$timestamp",
    'money'=>10.01,
    'timestamp'=>"$timestamp",
    'type'=>1,
    'source'=>'web',
    "callback_url"=>"http://www.snsshop.com",
    "taxpayer_num"=>"9144030007437956X1",
    "tax_name"=>"盛灿科技",
    "card_id"=>"plN5twRtkBnzwHup_UYoSC5n8c2o",
    "goods_info"=>[
        [
            "name"=>"商品1",
            "tax_code"=>"1020202000000000000",
            "total_price"=>10.01,
            "total"=>2,
            "tax_rate"=>0.13,
            "tax_amount"=>1.30,
        ]
    ],
];
$url = $host . '/authorize/collect-invoice-title?signature='.Demo1::generate_sign($post_data, $timestamp).'&timestamp='.$timestamp.'&sn='.$timestamp.$timestamp.'&appkey='.Demo1::$appkey;
echo $url.'<br />';*/

//发票开具(有用户抬头)
$post_data = [
    'app_id'=>Demo1::$wxappid,
    'order_id'=>"$timestamp",
    'money'=>10.01,
    'timestamp'=>"$timestamp",
    'type'=>0,
    'source'=>'wap',
    "callback_url"=>"http://www.snsshop.com",
    "taxpayer_num"=>"9144030007437956X1",
    "tax_name"=>"盛灿科技",
    "buyer_title"=>"张先生",
    "buyer_title_type"=>1,
    "card_id"=>"plN5twRtkBnzwHup_UYoSC5n8c2o",
    "goods_info"=>[
        [
            "name"=>"商品1",
            "tax_code"=>"1020202000000000000",
            "total_price"=>10.01,
            "total"=>2,
            "tax_rate"=>0.13,
            "tax_amount"=>1.30,
        ]
    ],
];
$url = $host . '/authorize/authurl-invoice-card?signature='.Demo1::generate_sign($post_data, $timestamp).'&timestamp='.$timestamp.'&sn='.$timestamp.$timestamp.'&appkey='.Demo1::$appkey;
echo $url.'<br />';

$res = CurlHelper::http_post($url, $post_data, 'json');
var_dump($res);
echo '<br />';
$res = json_decode($res,true);
var_dump($res);
echo '<br />';