<?php
/**
 * App Category | app/Library/Payment/NetpayGateway.php
 *
 * @package     self-checkout
 * @subpackage  Library
 * @author      chenmiao<陈淼><382169722@qq.com>
 * @version     v.1.0.1 (06/12/2016)
 * @copyright   Copyright (c) 2016, honglingjin.cn
 */
namespace App\Library\Payment;

/**
 * 招行一网通支付
 * @link http://58.61.30.110/OpenAPI2/API/PWDPayAPI4.aspx 接口文档
 */
class Helper {
    
    /**
     * @var string 日志名
     */
    const LOG_NAME = 'ccb_netpay';


    /**
     * 签名字段排序
     * 
     * @param array $reqData  签名字段
     * @return array
     */
    public static function dictSort( $reqData ) {
        $keyArr = [];
        $keyArrSorted = [];
        foreach ($reqData as $key => $val) {
            array_push($keyArr, strtolower($key));
        }
        sort($keyArr);

        for ($i = 0; $i < count($keyArr); $i++) {
            foreach ($reqData as $key => $val) {
                if (!strcasecmp($key, $keyArr[$i])) {
                    $keyArrSorted[$key] = $val;
                }
            }
        }
        return $keyArrSorted;
    }
    /**
     * sha256 签名
     * 
     * @param array $reqData  签名字段
     * @param string $signKey  签名密钥
     * @return string
     */
    public static function signBySha256( $reqData, $signKey = '' ) {
        $strToSign = '';
        $sortReqData = self::dictSort($reqData);
        foreach ($sortReqData as $key => $val) {
            $strToSign = $strToSign . $key . "=" . $val . "&";
        }
        $strToSign = $strToSign . $signKey;

        $strEncrypt = hash('sha256', $strToSign);
        return $strEncrypt; 
    }
    
    public static function isValidSignature( $reqData, $pubKey, $sign ) {
        try {
            $signParmArr = array();
            $sortReqData = self::dictSort($reqData);
            foreach ($sortReqData as $key => $val) {
                $signParmArr[] = $key . "=" . $val;
            }
            $signParmStr = empty($signParmArr) ? '' : implode('&', $signParmArr);
            
            $pem = chunk_split($pubKey, 64, "\n");
            $pem = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
            $pkid = openssl_pkey_get_public($pem);
            if (empty($pkid)) {
                return '获取 pkey 失败';
            }
            //验证
            $ok = openssl_verify($signParmStr, base64_decode($sign), $pkid, OPENSSL_ALGO_SHA1);
            return $ok;
        } catch (Exception $exc) {
            \App\Library\Log\Logger::info($exc->getMessage(), self::LOG_NAME);
            return $exc->getMessage();
        }
    }
    
}
