<?php

class RsaHelper {
    /**
     * 使用公钥加密数据
     * @param $originData
     * @param $publicKey
     * @return string
     * @throws Exception
     */
    public static function encrypt_data_public($originData,$publicKey)
    {
        $dataLength =  strlen($originData);
        $chunkLen = (1024/8-11);
        $round  = ceil($dataLength/(128 - 11));
        $encryptData = '';
        $publicKey = "-----BEGIN PUBLIC KEY-----\n"
                    .wordwrap($publicKey, 64, "\n", true)
                    ."\n-----END PUBLIC KEY-----";
        $puKey = openssl_pkey_get_public($publicKey);
        if(!$puKey){
           throw new \Exception('wrong publicKey');
        }
        for($i=0; $i<$round; $i++){
            $dataRow = '';
            if(openssl_public_encrypt(substr($originData, $chunkLen*$i,$chunkLen), $dataRow, $puKey)){
                $encryptData .= $dataRow;
            }
        }
        return base64_encode($encryptData);
    }

    /**
     * 使用私钥解密数据
     * @param $originData
     * @param $privateKey
     * @return string
     * @throws Exception
     */
    public static function decrypt_data_private($originData,$privateKey)
    {
        $base64_decode_data = base64_decode($originData);
        $round = ceil(strlen($base64_decode_data)/128);
        $decryptData= '';
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n"
                    . wordwrap($privateKey, 64, "\n", true)
                    ."\n-----END RSA PRIVATE KEY-----";
        $pikey = openssl_pkey_get_private($privateKey);
        if(!$pikey){
            throw new \Exception('wrong privateKey');
        }
        for($i=0; $i<$round; $i++){
            $data_row = '';
            if(openssl_private_decrypt(substr($base64_decode_data,$i*128,128), $dataRow, $pikey)){
                $decryptData .= $dataRow;
            }
        }
        return $decryptData;
    }

    /**
     * 私钥创建签名
     * @param $content
     * @param $privateKey
     * @param string $signType
     * @return string
     */
    public static function create_sign($content,$privateKey,$signType='RSA'){
        $piKey = "-----BEGIN RSA PRIVATE KEY-----\n"
                .wordwrap($privateKey, 64, "\n", true)
                ."\n-----END RSA PRIVATE KEY-----";
        if ("RSA2" == $signType) {
            openssl_sign($content, $signature, $piKey, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($content, $signature, $piKey);
        }
        $signature = base64_encode($signature); 
        return $signature;
    }

    /**
     * 公钥验证签名
     * @param $content
     * @param $signature
     * @param $publicKey
     * @param string $signType
     * @return int
     */
    public static function verify_sign($content,$signature,$publicKey,$signType='RSA'){
        $publicKey = "-----BEGIN PUBLIC KEY-----\n"
                    .wordwrap($publicKey, 64, "\n", true)
                    ."\n-----END PUBLIC KEY-----";
        if ("RSA2" == $signType) {
            $result = openssl_verify($content, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256);
        } else {
            $result = openssl_verify($content, base64_decode($signature), $publicKey);
        }
        return $result;
    }
}