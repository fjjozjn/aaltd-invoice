<?php
require('RsaHelper.php');
require('CurlHelper.php');
date_default_timezone_set('Asia/ShangHai');

class Demo2{

    /**
     * 校验签名
     * @param $params
     * @param $public_key
     * @param $sign_type
     * @return int
     */
    public static function check_sign($params,$public_key,$sign_type)
    {
        $signature = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $content = "";
        foreach($params as $key=>$val){
            if(is_array($val)){
                $val = json_encode($val);
            }
            $content .= "$key"."="."$val" ."&";
        }
        return RsaHelper::verify_sign(rtrim($content,'&'), $signature, $public_key,$sign_type);
    }

    /**
     * 生成带签名数组
     * @param array $params
     * @param string $private_key
     * @param string $sign_type
     * @return array
     */
    public static function generate_sign($params,$private_key,$sign_type)
    {
        if(isset($params['sign'])){
            unset($params['sign']);
        }
        ksort($params);
        $content = "";
        foreach($params as $key=>$val){
            if(is_array($val)){
                $val = json_encode($val,JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);
            }
            $content .= "$key"."="."$val" ."&";
        }
        $params['sign'] = RsaHelper::create_sign(rtrim($content,'&'),$private_key,$sign_type);
        return $params;
    }
}

/*$public_secret = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCLeBNU4gHCgbze2SxZJe4f+t28FKjur2FpRJ9mtvqcMUXVOvJKU+7W/TZYyv75gaPWrvg04W/xlp1q8G7XoEH+9EmTruMORxcP0B6j7VrptMeLg1XeOh3u/hjNmo7OXMXrWjHBQUEn8u2N0WJc43HEWrdHq879yBzfELQomjpd9wIDAQAB';
$private_secret = 'MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBAIt4E1TiAcKBvN7ZLFkl7h/63bwUqO6vYWlEn2a2+pwxRdU68kpT7tb9NljK/vmBo9au+DThb/GWnWrwbtegQf70SZOu4w5HFw/QHqPtWum0x4uDVd46He7+GM2ajs5cxetaMcFBQSfy7Y3RYlzjccRat0erzv3IHN8QtCiaOl33AgMBAAECgYEAgBttdlyazTUqbXACgmiK0Ck4n1hCy+ugiHcWC90rWWkRAgnBIHD0Q9oWn3zms/xWqA3Tw48Hqlt0gRQUXE/CDbWYsdOaklqiW2NwDKwoShLHI7ehl4i7mIB7afP/XtM/e0Ey5v8YwX+iv2w/HSgy4tQ98XgyHhbys9vtQ2+hysECQQDY7j6DaTL+ZGjDO0MIzbd4vqsWxrXnWCtrWNl5lhOixo9y/xde+cwaXgUnl+VZ7liVURkVrTt3EvEEFf6jRPMxAkEApJZnevtF27vfRWHvtlr4vi1WLRq3KM1/nadn1BQP096EnWws5iZSYL72lMFNAVgH/+DJX4nPrqt+EyUlLCMJpwJBAIGXefLpMpR0iX0v9vDYbCWOygh2Nw74Rh//2RNgi2RveBy7tUuAsOSDjFLF1DawQ20YIIMplN+iiibluNPyidECQFLK0d0cXyTMmeCmBlZ95piL58iiorYkwWhF2MXnHZsfWShzTRas+k1uYk2r+xeM9+Ewazvi8BTWcYIh8lQEgAsCQQDNR8W3Kyc7h6Y54HdgPl7QwA3Smy9/RgDBOTkXLXf6zHVuUUjKDgP9Q4N2V5gHzECWNGJZBeIga5Y3lxqDReG6';

$post_data = array(
    'app_id'=>'4006000100005233',
    'charset'=>'utf-8',
    'timestamp'=> date('Y-m-d H:i:s'),
    'format'=>'json',
    'method'=>'gaopeng.invoice.roll',
    'sign_type'=>'RSA2',
    "channel" => "GP110001",
    'biz_content'=>'{"buyer_title":"我的发票抬头","buyer_title_type":1,"buyer_taxcode":"123456789406426","buyer_address":"广东 深圳 南山","buyer_bank_name":"中国工商银行","buyer_bank_account":"621281240200099900000","buyer_phone":"18285162583","buyer_email":"Calvin.chen@gaopeng.com","result_call_back":"http://paper.yewifi.com/test/test","user_openid":"201708022e87777ggg74","b_unique_id":"20170802771231231321321321321231292","channel":"GP110001","machine_no":"0","sell_name":"百旺电子测试1","sell_tax_code":"110101201702071","sell_drawer":"李四","goods_info":[{"name":"商品1","tax_rate":"0.13","models":"XYZ","unit":"个","total_price":"10","total":2,"include_tax_flag":"1","tax_code":"1020202000000000000"},{"name":"商品2","tax_rate":"0.13","models":"XYZ","unit":"个","total_price":"10","total":2,"include_tax_flag":"0","tax_code":"1020202000000000000"}]}',
    'version'=>'v1.0'
);*/

$public_secret = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCqS4jsuDuybi0uOULM030kZNkcwEaB7TA1pTSE94OqM+ajcZM2WQdY77Ucx55Eg90MeL9pcYbR0IhgxUuinMimCQcOEUaygmE9AUwiqqMbAIV8wJYvdZP/wXNwDfqH1vr+VWQ92E9RM96I0vobnoeSd/4h1ozhZMc2WFLIewaAJwIDAQAB';
$private_secret = 'MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAKpLiOy4O7JuLS45QszTfSRk2RzARoHtMDWlNIT3g6oz5qNxkzZZB1jvtRzHnkSD3Qx4v2lxhtHQiGDFS6KcyKYJBw4RRrKCYT0BTCKqoxsAhXzAli91k//Bc3AN+ofW+v5VZD3YT1Ez3ojS+hueh5J3/iHWjOFkxzZYUsh7BoAnAgMBAAECgYAgLzpnksIljNEZJVVMpMPH9w9ZcbVhpNQxr1Fnt+e4aSqzxSlPYjATTOpr0AZHaKyESOXUJdKXINRFhcQvrXX2LBzAWuESuPlosES6L889Hl4xJ9GvMIZHSioUhtAx34SbW9ZbOe7vsnWfTBsIJuINbZpMURSmPkilxRUl+3QLUQJBAOJRMwOcleZH+wCfSiZJG20QGrQ2KSmK5t68GnPOI7l1H3E4k0PCQWJ48DHaALt9qeRT8sniHwajhZCRHRfW9JsCQQDAoVfGVCOYQGkyX9NWujYwt/ybbNF2yfq95d2VHYly1SML++ZuJUWxSsYF5fBFKyMTZtj1FcDcPXvVS8uzVm1lAkBG/g2wnlfvSrkT8uPBqsEtrxWRXkP/QhE14W+y0AEo1fKtA4A+ixsTlrhSCv6b0cmPVD4e0g3FeVoWhU7JAabtAkBkG7VtGc8B0f+ZsVk4yj9dZFDASXY2QNOxmuNkGlyTNBcppDjl5zLn548wA4axu7BX5ew8uphnHQDdIa30PqdNAkBnf0rd8KMC7gPMUA4i6+eSvHfdiaTe8TQimLKl90jj7vyIhDn++5S3trbFxqKj2qAns159Ub56nXeysOujyZVn';
/*{
    "buyer_title": "我的发票抬头",
	"buyer_title_type": 1,
	"buyer_taxcode": "123456789406426",
	"buyer_address": "广东 深圳 南山",
	"buyer_bank_name": "中国工商银行",
	"buyer_bank_account": "621281240200099900000",
	"buyer_phone": "18285162583",
	"buyer_email": "Calvin.chen@gaopeng.com",
	"result_call_back": "http://paper.yewifi.com/test/test",
	"user_openid": "201708022e87777ggg74",
	"b_unique_id": "20170802771231231321321321321231292",
	"channel": "GP110001",
	"machine_no": "0",
	"sell_name": "百旺电子测试1",
	"sell_tax_code": "110101201702071",
	"sell_drawer": "李四",
	"goods_info":
    [
        {
            "name": "商品1",
            "tax_rate": "0.13",
            "models": "XYZ",
            "unit": "个",
            "total_price": "10",
            "total": 2,
            "include_tax_flag": "1",
            "tax_code": "1020202000000000000"
        },
        {
            "name": "商品2",
            "tax_rate": "0.13",
            "models": "XYZ",
            "unit": "个",
            "total_price": "10",
            "total": 2,
            "include_tax_flag": "0",
            "tax_code": "1020202000000000000"
        }
    ]
}*/
$post_data = array(
    'app_id'=>'2017120476902176',
    'charset'=>'utf-8',
    'timestamp'=> date('Y-m-d H:i:s'),
    'format'=>'json',
    'sign_type'=>'RSA',
    'biz_content'=>'{"buyer_title":"我的发票抬头","buyer_title_type":1,"buyer_taxcode":"123456789406426","buyer_address":"广东 深圳 南山","buyer_bank_name":"中国工商银行","buyer_bank_account":"621281240200099900000","buyer_phone":"18285162583","buyer_email":"Calvin.chen@gaopeng.com","result_call_back":"http://paper.yewifi.com/test/test","user_openid":"201708022e87777ggg74","b_unique_id":"20170802771231231321321321321231292","channel":"GP110001","machine_no":"0","sell_name":"百旺电子测试1","sell_tax_code":"110101201702071","sell_drawer":"李四","goods_info":[{"name":"商品1","tax_rate":"0.13","models":"XYZ","unit":"个","total_price":"10","total":2,"include_tax_flag":"1","tax_code":"1020202000000000000"},{"name":"商品2","tax_rate":"0.13","models":"XYZ","unit":"个","total_price":"10","total":2,"include_tax_flag":"0","tax_code":"1020202000000000000"}]}',
    'version'=>'v1.0'
);


//签名
$post_data = Demo2::generate_sign($post_data,$private_secret,$post_data['sign_type']);
$url = 'http://paper.yewifi.com/invoice/index';
$res = CurlHelper::http_post($url, $post_data, 'json');
$res = json_decode($res,true);
var_dump($res);


//验签
$sign_result = Demo2::check_sign($post_data,$public_secret,$post_data['sign_type']);
if($sign_result != 1){
    echo '验签校验失败';
}else{
    echo '验签成功';
}
