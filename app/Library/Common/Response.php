<?php
/**
 * App Category | app/Library/Common/Response.php
 *
 * @package     self-checkout
 * @subpackage  Library
 * @author      chenmiao<陈淼><382169722@qq.com>
 * @version     v.1.0.1 (06/12/2016)
 * @copyright   Copyright (c) 2016, honglingjin.cn
 */

namespace App\Library\Common;

use Phalcon\Di;
/**
 * 通用输出类
 */
class Response {
    
    /**
     * @var int 页面未找到
     */
    const HTTP_STATUS_PAGE_NOT_FOUND = 404;
    /**
     * @var int 接口请求成功
     */
    const HTTP_STATUS_SUCCESS = 200;
    /**
     * @var int 接口请求失败
     */
    const HTTP_STATUS_FAILED = 10001;
    /**
     * @var int 解密失败
     */
    const HTTP_DECRYPT_FAILED = 10002;
    /**
     * @var int 缺少必要字段
     */
    const HTTP_STATUS_FAILED_MISSING_REQUIRE_FIELD = 10004;
    /**
     * @var int 用户未登陆
     */
    const HTTP_STATUS_FAILED_USER_NO_LOGIN = 10003;
    /**
     * @var int 服务器错误
     */
    const HTTP_STATUS_FAILED_SERVER_ERROR = 10005;
    /**
     * @var int 订单已支付
     */
    const HTTP_STATUS_FAILED_ORDER_HAS_PAY = 10006;
    /**
     * @var int 支付成功
     */
    const HTTP_STATUS_PAY_SUCCESS = 20000;
    /**
     * @var int 支付失败
     */
    const HTTP_STATUS_PAY_FAIL = 20001;
    /**
     * @var int 支付金额有误
     */
    const HTTP_STATUS_PAY_AMOUNT_ERROR = 20002;

    /**
     * 返回json格式
     * 
     * @param string $code          输出状态码
     * @param string $message       输出信息
     * @param array $data           输出字段
     * @return mixed
     */
    public static function responseJson( $code = NULL, $message = '', $extraData = array(), $data = array() ) {
        $response = Di::getDefault()->get('response');
        $response->setHeader('Content-Type', 'application/json');
        $retData = array( 'code' => $code, 'message' => $message,  'timestamp' => date('Y-m-d H:i:s') );
        if( !empty($data)  ) {
            $retData['data'] = $data;
        }
        $ret = array_merge($retData, $extraData);
        return $response->setJsonContent($ret);
    }
    /**
     * 输出json 并退出
     * 
     * @param string $code
     * @param string $message
     * @param array $extraData
     * @param array $data
     * @return void
     */
    public static function responseJsonAndExit( $code = NULL, $message = '', $extraData = array(), $data = array() ) {
        $retData = array( 'code' => $code, 'message' => $message, 'timestamp' => date('Y-m-d H:i:s') );
        if( !empty($data)  ) {
            $retData['result'] = $data;
        }
        $ret = array_merge($retData,$extraData);
        exit(json_encode($ret));
    }
    /**
     * 跳转
     * 
     * @param string $location
     * @param string $externalRedirect
     * @param array $statusCode
     * @return mixed
     */
    public static function redirect($location = null, $externalRedirect = false, $statusCode = 302) {
         $ret = Di::getDefault()->get('response')->redirect($location, $externalRedirect, $statusCode);
         return $ret;
    }
    /**
     * 输出文本并退出
     * 
     * @param string $text
     * @return void
     */
    public static function responseTextAndExit( $text ) {
        exit($text);
    }
    /**
     * 输出json并退出
     * 
     * @param array $data
     * @return void
     */
    public static function responseJsonByArrayAndExit( $data ) {
        exit(json_encode($data));
    }
}
