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

use App\Library\Payment\Helper;
/**
 * 招行一网通支付
 * @link http://58.61.30.110/OpenAPI2/API/PWDPayAPI4.aspx 接口文档
 */
class Netpay {
    
    /**
     * @var string 支付api版本
     */
    const NETPAY_API_VERSION = '1.0';
    /**
     * @var string 参数编码,固定为“UTF-8”
     */
    const NETPAY_CHARSET = 'UTF-8';
    /**
     * @var string 签名算法,固定为“SHA-256”.
     */
    const NETPAY_SIGN = 'SHA-256';
    /**
     * @var string 默认扩展信息的加密算法
     */
    const NETPAY_DEFAULT_EXTENDINFO_ENCRYP_TYPE = 'RC4';
    /**
     * @var string 默认到期时间 必须为大于零的整数，单位为分钟。
     */
    const NETPAY_DEFAULT_EXPIRE_TIME = '30';
    /**
     * @var string 支付接口地址-沙盒环境
     */
    const API_ENDPOINT_PAY_SANDBOX = 'http://61.144.248.29:801/netpayment/BaseHttp.dll?MB_EUserPay';
    /**
     * @var string 签名接口地址-沙盒环境
     */
    const API_ENDPOINT_SIGN_SANDBOX = 'http://58.61.30.110/CmbBank_B2B/UI/NetPay/DoBusiness.ashx';
    /**
     * @var string 支付接口地址-生产环境
     */
    const API_ENDPOINT_PAY_PRODUCTION = 'https://netpay.cmbchina.com/netpayment/BaseHttp.dll?MB_EUserPay';
    /**
     * @var string 签名接口地址-生产环境
     */
    const API_ENDPOINT_SIGN_PRODUCTION = 'https://b2b.cmbchina.com/CmbBank_B2B/UI/NetPay/DoBusiness.ashx';
    /**
     * @var string 生产环境
     */
    const API_ENV_PRODUCTION = 'production';
    /**
     * @var string 沙盒环境
     */
    const API_ENV_SANDBOX = 'sandbox';
    /**
     * @var string 当前环境
     */
    protected $currentEnv = 'sandbox';

    /**
     * @var App\Library\Payment\Netpay 
     */
    private static $netPay = NULL;
    /**
     * @var string 支付公钥
     */
    protected $signKey = '';
    /**
     * @var string 商户分行号，4位数字
     */
    protected $branchNo = '';
    /**
     * @var string 商户号，6位数字
     */
    protected $merchantNo = '';
    /**
     * @var array 支付所需参数
     */
    protected $payload = array();
    /**
     * @var string 支付回调地址
     */
    protected $noticeUrl = '';
    /**
     * @var string 支付完成返回地址
     */
    protected $returnUrl = '';
    /**
     * @var string 签约成功通知地址
     */
    protected $signNoticeUrl = '';

    /**
     * @var array 
     */
    protected $payParameterBag = array(
        'version'   => '1.0',
        'charset'   => 'UTF-8',
        'sign'      => '',
        'signType'  => 'SHA-256',
        'reqData'   => array(
            'dateTime'              => '',
            'branchNo'              => '',
            'merchantNo'            => '',
            'date'                  => '',
            'orderNo'               => '',
            'amount'                => '',
            'expireTimeSpan'        => self::NETPAY_DEFAULT_EXPIRE_TIME,
            'payNoticeUrl'          => '',
            'payNoticePara'         => '',
            'returnUrl'             => '',
            'agrNo'                 => '',
            'merchantSerialNo'      => '',
            'signNoticeUrl'         => '',
            'signNoticePara'        => '',
            'extendInfo'            => '',
            'extendInfoEncrypType'  => self::NETPAY_DEFAULT_EXTENDINFO_ENCRYP_TYPE
        )
    );
    
    protected $queryPubKeyParameterBag = array(
            'version'   => '1.0',
            'charset'   => 'UTF-8',
            'sign'      => '',
            'signType'  => 'SHA-256',
            'reqData'   => array(
                'dateTime'      => '',
                'txCode'        => 'FBPK',
                'branchNo'      => '',
                'merchantNo'    => ''
            )
    );
    
    /**
     * @param \Phalcon\Config $config
     * @return void
     */
    private function __construct(  ) {

    }
    /**
     * 设置签名key
     * 
     * @param string $key
     * @return void
     */
    public function setSignKey( $key = '' ) {
        $this->signKey = $key;
    }
    /**
     * 设置商户分行号
     * 
     * @param string $branchNo
     * @return void
     */
    public function setBranchNo( $branchNo ) {
        $this->branchNo = $branchNo;
    }
    /**
     * 设置商户号，6位数字
     * 
     * @param string $merchantNo
     * @return void
     */
    public function setMerchantNo( $merchantNo ) {
        $this->merchantNo = $merchantNo;
    }
    /**
     * 设置支付回调地址
     * 
     * @param string $noticeUrl
     * @return void
     */
    public function setNoticeUrl( $noticeUrl ) {
        $this->noticeUrl = $noticeUrl;
    }
    /**
     * 设置支付成功返回地址
     * 
     * @param string $returnUrl 
     * @return void
     */
    public function setReturnUrl( $returnUrl ) {
        $this->returnUrl = $returnUrl;
    }
    /**
     * 设置签约成功回调地址
     * 
     * @param string $signNoticeUrl
     * @return void
     */
    public function setSignNoticeUrl( $signNoticeUrl ) {
        $this->signNoticeUrl = $signNoticeUrl;
    }
    /**
     * 设置运行环境
     * 
     * @param string $env 
     * @return void
     */
    public function setEnv( $env ) {
        $this->currentEnv = $env;
    }

    public function setPayload( $data = array() ) {
        $reqData = &$this->payParameterBag['reqData'];
        $reqData['branchNo'] = $this->branchNo;
        $reqData['merchantNo'] = $this->merchantNo;
        $reqData['signNoticeUrl'] = $this->signNoticeUrl;
        $reqData['payNoticeUrl'] = $this->noticeUrl;
        $reqData['returnUrl'] = $this->returnUrl;
        $reqData = array_merge($reqData, $data);
        $sortReqData = Helper::dictSort($reqData);
        $sign = Helper::signBySha256($sortReqData, $this->signKey);
        $this->payParameterBag['sign'] = $sign;
        $this->payload = $this->payParameterBag;
    }
    
    public function getPayload( ) {
        return $this->payload;
    }

    /**
     * 
     * @param  \Phalcon\Config $config
     * @return \App\Library\Payment\Netpay;
     */
    public static function createPayRequest( $config = array() ) {
        if( self::$netPay instanceof Netpay ) {
            return self::$netPay;
        }
        self::$netPay = new self( $config );
        return self::$netPay;
    }
    
    public function getPublicKey() {
        $reqData = &$this->queryPubKeyParameterBag['reqData'];
        $reqData['branchNo'] = $this->branchNo; 
        $reqData['merchantNo'] = $this->merchantNo;
        $reqData['dateTime'] = date('YmdHis', time());
        $sortReqData = Helper::dictSort($reqData);
        $sign = Helper::signBySha256($sortReqData, $this->signKey);
        $this->queryPubKeyParameterBag['sign'] = $sign;
        $queryPublicKeyApi = $this->getSignEndPoint();
        $result = $this->httpPost($this->queryPubKeyParameterBag, $queryPublicKeyApi);
        $data = empty($result) ? array() : json_decode($result, TRUE);
        $fbPubKey = '';
        if (isset($data['rspData']['fbPubKey']) && !empty($data['rspData']['fbPubKey'])) {
            $fbPubKey = $data['rspData']['fbPubKey'];
        }
        return $fbPubKey;        
        
    }

    /**
     * 获取支付地址
     * 
     * @return string
     */
    public function getPayEndPoint() {
        $payEndPoint = '';
        switch ( $this->currentEnv ) {
            case self::API_ENV_PRODUCTION:
                $payEndPoint = self::API_ENDPOINT_PAY_PRODUCTION;
                break;
            case self::API_ENV_SANDBOX:
                $payEndPoint = self::API_ENDPOINT_PAY_SANDBOX;
                break;
            default :
                $payEndPoint = self::API_ENDPOINT_PAY_SANDBOX;
        }
        return $payEndPoint;
    }
    
    /**
     * 获取签名地址
     * 
     * @return string
     */
    public function getSignEndPoint() {
        $payEndPoint = '';
        switch ( $this->currentEnv ) {
            case self::API_ENV_PRODUCTION:
                $payEndPoint = self::API_ENDPOINT_SIGN_PRODUCTION;
                break;
            case self::API_ENV_SANDBOX:
                $payEndPoint = self::API_ENDPOINT_SIGN_SANDBOX;
                break;
            default :
                $payEndPoint = self::API_ENDPOINT_SIGN_SANDBOX;
        }
        return $payEndPoint;  
    }


    /**
     * 获取post数据
     * 
     * @param array $parasData 提交数组
     * @return string
     */
    protected function getPostData( $parasData = array() ) {
        $reqDataStr = mb_convert_encoding(json_encode($parasData, JSON_UNESCAPED_UNICODE), self::NETPAY_CHARSET);
        $jsonData = 'jsonRequestData=' . $reqDataStr;
        return $jsonData;
    }

    /**
     * curl post请求
     * 
     * @param array $parasData  post数组
     * @param string $url       http请求URL地址
     * @return string
     */
    protected function httpPost( $parasData, $url ) {
        $ch = curl_init();
        $postData = $this->getPostData($parasData);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    
    private function __clone() {
        
    }
}
