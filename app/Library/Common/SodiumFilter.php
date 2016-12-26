<?php
/**
 * App Category | app/Library/Common/SodiumFilter.php
 *
 * @package     self-checkout
 * @subpackage  Library
 * @author      chenmiao<陈淼><382169722@qq.com>
 * @version     v.1.0.1 (06/12/2016)
 * @copyright   Copyright (c) 2016, honglingjin.cn
 */

namespace App\Library\Common;

use Phalcon\Di;
use App\Library\Log\Logger;

/**
 * Sodium 库 过滤器
 */
class SodiumFilter
{
    
    /**
     * @var array 过滤规则
     */
    protected $filterRule = array();
    /**
     * @var string  服务端私钥
     */
    protected $serverSecretKey = '';
    /**
     * @var string 服务端公钥
     */
    protected $serverPubKey = '';
    
    /**
     * @var boolean 加密功能是否启用
     */
    protected $enable = false;


    /**
     * @var array 配置文件属性
     */
    private $configProperty = array(
        'filterRule', 'serverSecretKey', 'serverPubKey', 'enable'
    );


    public function __construct()
    {
        $config = Di::getDefault()->getConfig();
        foreach ($this->configProperty as $property) {
            if (isset($config->sodiumFilter->{$property})) {
                $this->{$property} = $config->sodiumFilter->{$property};
            } else {
                $this->{$property} = NULL;
            }
        }
    }
    
    /**
     * 解密密文
     *
     * @param string $rawString 密文
     * @return mixed
     */
    public function getDecryptStream($rawText = "", $clientPublicKey = "", $clientNonce = "")
    {   
        $ret = FALSE;
        if( !$this->isEncryptVerion() || !$this->isOpen() ) {
            $ret = $rawText;
        } else {
            try {
                $nonce = new \sodium\nonce();
                $crypto = new \sodium\crypto();
                $publicKey = new \sodium\public_key();
                $publicKey->load($clientPublicKey, true);
                $secretKey = new \sodium\secret_key();
                $secretKey->load($clientPublicKey, $this->serverSecretKey, true);
                $decryptedText = $crypto->box_open(
                    base64_decode($rawText),
                    $nonce->set_nonce(base64_decode($clientNonce), true),
                    $publicKey,
                    $secretKey
                );
                $logMessage = "nonce:$clientNonce \r\n clientPublicKey:$clientPublicKey \r\n box_open: $decryptedText";
                Logger::info($logMessage, 'sodium');
                $ret = $decryptedText;
            } catch (\sodium\crypto_exception $e) {
                $_REQUEST['decryptedText'] = $e->getMessage();
                $msg = sprintf('%s->%s(%s,%s,%s) Error:%s', __CLASS__, __METHOD__, $rawText, $clientPublicKey, $clientNonce, $e->getMessage());
                Logger::info("crypto_exception:" . $msg, 'sodium');
                $ret = false;
            }
        }
        return $ret;
    }
    
    /**
     * 解析数组，并重新填充$_REQUEST 超全局变量
     *
     * @return boolean
     */
    public function decryptStream()
    {
        $rawString = file_get_contents('php://input', 'r');
        $clientPublicKey = Helper::getClientPublicKey();
        $clientNonce = Helper::getClientNonce();
        $ret = FALSE;
//        if( empty($rawString) ) {
//            $ret = TRUE;
//        } elseif( !empty($clientPublicKey) && !empty($clientNonce) ) {
        if( !empty($clientPublicKey) && !empty($clientNonce) ) {
            Logger::info("decryptStream:" . $clientPublicKey . "=>" . $clientNonce, 'sodium');
            $decryptStream = $this->getDecryptStream($rawString, $clientPublicKey, $clientNonce);
            if (false !== $decryptStream) {
                parse_str($decryptStream, $dataArr);
                $_REQUEST = array_merge($_REQUEST, $dataArr);
                $ret = TRUE;
            }   
        }
        return $ret;
    }
    
    /**
     * 判断当前版本是否需要解密
     *
     * @return boolean
     */
    public function isEncryptVerion()
    {
        $ret = FALSE;
        $deviceType = Helper::getDeviceType();
        $currentVersion = Helper::getAppVerion();
        $ruleVersion = $this->filterRule->get($deviceType, FALSE);
        if( !class_exists('\\sodium\\nonce') || ( FALSE === $ruleVersion )  ) {
            $errorMessage = sprintf('[isEncryptVerion] \\sodium\\nonce=%d ruleVersion=%s', class_exists('\\sodium\\nonce'), $ruleVersion);
            Logger::info($errorMessage, 'sodium');
            $ret = FALSE;
        } else {
             $ret = version_compare($ruleVersion, $currentVersion) <= -1;
        }
        return $ret;
    }
    
    /**
     * 判断当前功能是否启用
     *
     * @return boolean
     */
    public function isOpen()
    {
        return $this->enable;
    }
}
