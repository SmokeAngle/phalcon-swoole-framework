<?php
/**
 * App Category | app/Library/Common/Helper.php
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
 * 功能辅助类
 */
class Helper {

    /**
     * @var string 安卓设备名
     */
    const APP_DEVICE_NAME_ANDROID = DEVICE_NAME_ANDRIOD;

    /**
     * @var string iPhone 设备名
     */
    const APP_DEVICE_NAME_IPHONE = DEVICE_NAME_IPHONE;

    /**
     * @var string IPad 设备名
     */
    const APP_DEVICE_NAME_IPAD = DEVICE_NAME_IPAD;

    /**
     * @var string wap设备名
     */
    const APP_DEVICE_NAME_WAP = DEVICE_NAME_WAP;

    /**
     * @var string wap_jljy警乐警员app设备名
     */
    const APP_DEVICE_NAME_WAP_JLJY = DEVICE_NAME_WAP_JLJY;

    /**
     * @var string 未知设备
     */
    const APP_DEVICE_NAME_UNKNOWN = 'unknown';

    /**
     * @var string 等于
     */
    const CONDITION_FLAG_EQ = 'eq';

    /**
     * @var string 不等于
     */
    const CONDITION_FLAG_NEQ = 'neq';

    /**
     * @var string 大于
     */
    const CONDITION_FLAG_GT = 'gt';

    /**
     * @var string 大于或等于
     */
    const CONDITION_FLAG_EGT = 'egt';

    /**
     * @var string 小于
     */
    const CONDITION_FLAG_LT = 'lt';

    /**
     * @var string 小于或等于
     */
    const CONDITION_FLAG_ELT = 'elt';

    public static $deviceType = array(
        DEVICE_NAME_IPHONE => 1,
        DEVICE_NAME_IPAD => 4,
        DEVICE_NAME_ANDRIOD => 2,
        DEVICE_NAME_ANDRIODPAD => 5,
        DEVICE_NAME_ANDRIODTV => 6,
        DEVICE_NAME_WAP => 0,
        DEVICE_NAME_WAP_JLJY => 7,
    );

    /**
     * 检查当前版本
     *
     * @param string $currentVerion   当前版本
     * @param array $conditions       版本匹配条件
     * @return boolean
     */
    public static function checkVerion($currentVerion = "", $conditions = array()) {
        if (!empty($conditions) && is_array($conditions)) {
            while ($flag = key($conditions)) {
                switch ($flag) {
                    case self::CONDITION_FLAG_EGT:
                        if (!( version_compare($currentVerion, $conditions[$flag]) >= 0 )) {
                            return false;
                        }
                        break;
                    case self::CONDITION_FLAG_GT:
                        if (!( version_compare($currentVerion, $conditions[$flag]) > 0 )) {
                            return false;
                        }
                        break;
                    case self::CONDITION_FLAG_LT:
                        if (!( version_compare($currentVerion, $conditions[$flag]) < 0 )) {
                            return false;
                        }
                        break;
                    case self::CONDITION_FLAG_ELT:
                        if (!( version_compare($currentVerion, $conditions[$flag]) )) {
                            return false;
                        }
                        break;
                    case self::CONDITION_FLAG_EQ:
                        if (!( version_compare($currentVerion, $conditions[$flag]) )) {
                            return false;
                        }
                        break;
                    case self::CONDITION_FLAG_NEQ:
                        if (!( version_compare($currentVerion, $conditions[$flag]) )) {
                            return false;
                        }
                        break;
                }
                next($conditions);
            }
        }
        return true;
    }

    /**
     * 获取当前设备类型
     *
     * @return string
     */
    public static function getDeviceType() {
        array_walk(self::$deviceType, function(&$item, $key) {
            unset(self::$deviceType[$key]);
            $key = strtolower($key);
            self::$deviceType[$key] = $item;
        });
        if (false != ( $appDeviceType = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_DEVICETYPE') )) {
            $appDeviceType = strtolower($appDeviceType);
            if (array_key_exists($appDeviceType, self::$deviceType)) {
                return $appDeviceType;
            }
        }
        Logger::warning('未知设备类型：HTTP_X_HTTP_DEVICETYPE = ' . $appDeviceType, 'helper');
        return self::APP_DEVICE_NAME_UNKNOWN;
    }

    // Create a new helper
    public static function config($key) {
        return DI::getDefault()->getConfig()->$key;
    }

    /**
     * 获取当前APP 版本号
     *
     * @return string
     */
    public static function getAppVerion() {
        $appVersion = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_VERSION');
        return $appVersion;
    }

    /**
     * 获取当前设备的UID
     *
     * @return string
     */
    public static function getDeviceUid() {
        $appDeviceUid = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_DEVICEUID');
        return $appDeviceUid;
    }

    /**
     * 获取当前设备的token
     * (此次为信鸽推送)
     * @return string
     */
    public static function getDeviceToken() {
        $appDeviceToken = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_DEVICETOKEN');
        return $appDeviceToken;
    }
    
    
    /**
     * 获取当前设备的token
     * (此次为极光推送)
     * @return type
     */
    public static function getJGDeviceToken() {
        $appDeviceToken = Di::getDefault()->getRequest()->getHeader('HTTP_J_HTTP_DEVICETOKEN');
        return $appDeviceToken;
    }
    

    /**
     * 登陆后的token
     *
     * @return string
     */
    public static function getToken() {
        $appToken = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_TOKEN');
        Logger::info('登陆后的token = ' . $appToken, 'helper');
        return $appToken;
    }

    /**
     * 登陆后的用户ID
     *
     * @return string
     */
    public static function getMemberId() {
        if( FALSE === ( $memberId = Di::getDefault()->getRequest()->get('user_id', NULL, FALSE) ) ) {
            $memberId = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_MEMBER');
        }
        return $memberId;
    }

    /**
     * 获取客户端公钥
     *
     * @return string
     */
    public static function getClientPublicKey() {
        $clientPubKey = Di::getDefault()->getRequest()->getHeader('HTTP_X_CLIENT_PUBKEY');
        return $clientPubKey;
    }

    /**
     * 获取客户端随机数
     *
     * @return string
     */
    public static function getClientNonce() {
        $clientNonce = Di::getDefault()->getRequest()->getHeader('HTTP_X_CLIENT_NONCE');
        return $clientNonce;
    }

    /**
     * 获取User-Agent
     *
     * @return string
     */
    public static function getUserAgent() {
        $userAgent = Di::getDefault()->getRequest()->getHeader('HTTP_USER_AGENT');
        return $userAgent;
    }

    /**
     * 获取X_HTTP_PACKAGE
     *
     * @return string
     */
    public static function getAppPackage() {
        $appPackage = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_PACKAGE');
        return $appPackage;
    }

    /**
     * 获取X_HTTP_TIME
     *
     * @return string
     */
    public static function getTimestamp() {
        $requestTime = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_TIME');
        return $requestTime;
    }

    /**
     * 获取INTERFACE_V
     *
     * @return string
     */
    public static function getInterfaceV() {
        $interfaceV = Di::getDefault()->getRequest()->getHeader('HTTP_X_HTTP_INTERFACE_V');
        return $interfaceV;
    }

    /**
     * 获取客户端真实IP
     *
     * @return string
     */
    public static function getRealIp() {
        if (isset($_SERVER['HTTP_QVIA'])) {
            $ip = self::getRealIpByQvia($_SERVER['HTTP_QVIA']);
            if ($ip) {
                return trim($ip);
            }
        }
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            return self::isVaildIp($_SERVER['HTTP_CLIENT_IP']) ? trim($_SERVER['HTTP_CLIENT_IP']) : '0.0.0.0';
        }
        if (isset($_SERVER['HTTP_TRUE_CLIENT_IP']) && !empty($_SERVER['HTTP_TRUE_CLIENT_IP'])) {
            return self::isVaildIp($_SERVER['HTTP_TRUE_CLIENT_IP']) ? $_SERVER['HTTP_TRUE_CLIENT_IP'] : '0.0.0.0';
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
            do {
                $tmpIp = explode('.', $ip);
                if (is_array($tmpIp) && count($tmpIp) == 4) {
                    if (($tmpIp[0] != 10) && ($tmpIp[0] != 172) && ($tmpIp[0] != 192) && ($tmpIp[0] != 127) && ($tmpIp[0] != 255) && ($tmpIp[0] != 0)) {
                        return trim($ip);
                    }
                    if (($tmpIp[0] == 172) && ($tmpIp[1] < 16 || $tmpIp[1] > 31)) {
                        return trim($ip);
                    }
                    if (($tmpIp[0] == 192) && ($tmpIp[1] != 168)) {
                        return trim($ip);
                    }
                    if (($tmpIp[0] == 127) && ($ip != '127.0.0.1')) {
                        return trim($ip);
                    }
                    if ($tmpIp[0] == 255 && ($ip != '255.255.255.255')) {
                        return trim($ip);
                    }
                    if ($tmpIp[0] == 0 && ($ip != '0.0.0.0')) {
                        return trim($ip);
                    }
                }
            } while (($ip = strtok(',')));
        }
        if (isset($_SERVER['HTTP_PROXY_USER']) && !empty($_SERVER['HTTP_PROXY_USER'])) {
            return self::isVaildIp($_SERVER['HTTP_PROXY_USER']) ? trim($_SERVER['HTTP_PROXY_USER']) : '0.0.0.0';
        }

        if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
            return self::isVaildIp($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
        } else {
            return '0.0.0.0';
        }
    }

    /**
     * 判断当前请求平台
     *
     * @return boolean
     */
    public static function isAppPlatform() {
        $deviceType = Helper::getDeviceType();
        if ($deviceType === DEVICE_NAME_IPHONE || $deviceType === DEVICE_NAME_IPAD || $deviceType === DEVICE_NAME_ANDRIOD) {
            return true;
        }
        return false;
    }

    /**
     * 获取网通代理或教育网代理带过来的客户端IP
     *
     * @param string $qvia
     * @return boolean|string
     */
    public static function getRealIpByQvia($qvia) {
        if (strlen($qvia) != 40) {
            return false;
        }
        $ips = array(hexdec(substr($qvia, 0, 2)), hexdec(substr($qvia, 2, 2)), hexdec(substr($qvia, 4, 2)), hexdec(substr($qvia, 6, 2)));
        $ipbin = pack('CCCC', $ips[0], $ips[1], $ips[2], $ips[3]);
        $m = md5('QV^10#Prefix' . $ipbin . 'QV10$Suffix%');
        if ($m == substr($qvia, 8)) {
            return implode('.', $ips);
        } else {
            return false;
        }
    }

    /**
     * 检测IP地址格式是否合法
     *
     * @param string $ip IP地址
     * @return boolean
     */
    public static function isVaildIp($ip) {
        $ip = trim($ip);
        $pt = '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';
        if (preg_match($pt, $ip) === 1) {
            return true;
        }
        return false;
    }

    /**
     * 变量调试
     *
     * @return string
     */
    public static function dump($var, $echo = true, $label = null, $strict = true) {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        } else {
            return $output;
        }
    }

    /**
     * CURL POST请求
     * 
     * @param string $url  请求URL地址
     * @param array $data  post数据
     * @param array $headers  请求头
     * @return string
     */
    public static function curlPost($url, $data = null, $headers = array() ) {

        $ch = curl_init($url);
        $info = parse_url($url);
        $port = isset($info["port"]) ? $info["port"] : 80;
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PORT, $port); //设置端口
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
      
        if ( !empty($headers) ) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $ret = curl_exec($ch);
        curl_close($ch); 
        return $ret;
    }
    
    /**
     * 获取设备类型对应索引值
     * 
     * @return boolean|int
     */
    public static function getDeviceTypeIdx() {
        $deviceType = Helper::getDeviceType();
        if (array_key_exists($deviceType, self::$deviceType)) {
            return self::$deviceType[$deviceType];
        }
        return FALSE;
    }

    
    /*
     * 生产消息
     */
    public static function publishMQ($message){
        $config = Di::getDefault()->getShared('config');
        $connection = Di::getDefault()->getShared('amqpService')->getInstance()->connection;
        $channel = new \AMQPChannel($connection);
        
        // 声明交换机
        $exchange = new \AMQPExchange($channel);
        $exchange->setName($config->RabbitmqExchangeName);
        $exchange->setType(AMQP_EX_TYPE_DIRECT); //direct类型
        $exchange->setFlags(AMQP_DURABLE); //持久化
        $exchange->declareExchange();

        // 声明队列
        $queue = new \AMQPQueue($channel);
        $queue->setName($config->RabbitmqQueueName);
        $queue->setFlags(AMQP_DURABLE); //持久化
        $queue->declareQueue();
        
        // 绑定队列
        $queue->bind($config->RabbitmqExchangeName, $config->RabbitmqRouteKey);

        $res = $exchange->publish($message, $config->RabbitmqRouteKey);
        $connection->disconnect(); //释放连接
        return $res;
    }
}
