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

namespace App\Library\Events;

use Phalcon\Events\Event;
use Phalcon\Mvc\Micro;

use App\Library\Log\Logger;
use App\Library\Common\Response;
use App\Library\Common\SodiumFilter;
use App\Library\Common\Helper;

class beforeExecuteRouteEvent  {
    
    /**
     * @var \Phalcon\Events\Event $event
     */
    public $event;
    /**
     * @var \Phalcon\Mvc\Micro  $app
     */
    public $app;
    /**
     * @var \Phalcon\Mvc\Dispatcher  $dispatcher
     */
    public $dispatcher;
    /**
     * @var string $routerName 当前匹配路由名称
     */
    public $routerName;
    /**
     * @var \Phalcon\Config $routerConfig 当前路由设置
     */
    public $routerConfig;
    /**
     * @var string 加密设置配置key
     */
    const EVENT_CONFIG_KEY_SODIUM_DECRYPT = 'encrypt';
    /**
     * @var string 签名设置配置key
     */
    const EVENT_CONFIG_KEY_USER_TOKEN_IDENTIFY = 'singed';
    
    const LOG_NAME = 'Event.beforeExecuteRouteEvent';

    public function __construct(Event $event, Micro $app) {
        $this->event = $event;
        $this->app = $app;
        $this->routerName = $app->router->getMatchedRoute()->getName();
        $this->routerConfig = $app->config->router->get($this->routerName, new \Phalcon\Config());
    }
    
    public function execute() {
        $this->sodiumDecryptEvent();
        $this->userTokenIdentifyEvent();
    }
    
    /**
     * sodium 库解密事件
     */
    private function sodiumDecryptEvent() {
        $isDecrypt = $this->routerConfig->get(self::EVENT_CONFIG_KEY_SODIUM_DECRYPT, TRUE);
        if( TRUE === $isDecrypt ) {
            if( FALSE === ( (new SodiumFilter())->decryptStream()) ) {
                $message = sprintf("sodium decrypt error method=post request=%s", json_encode($_REQUEST));
                Logger::error($message, self::LOG_NAME);
                Response::responseJsonAndExit(Response::HTTP_DECRYPT_FAILED, 'sodium decrypt error');
            } 
        }
    }
    /**
     * 用户token验证事件
     */
    private function userTokenIdentifyEvent() {
        $isSinged = $this->routerConfig->get(self::EVENT_CONFIG_KEY_USER_TOKEN_IDENTIFY, TRUE);
        if( $isSinged ) {
            $token = Helper::getToken();
            $memberId = Helper::getMemberId();
            if( empty($memberId) ) {
                Response::responseJsonAndExit(Response::HTTP_STATUS_FAILED_MISSING_REQUIRE_FIELD, '亲，您的用户ID为空，请重新请求。');
            }
            if( empty($token) ) {
                Response::responseJsonAndExit(Response::HTTP_STATUS_FAILED_MISSING_REQUIRE_FIELD, '亲，非法请求(用户token为空)。');
            }            
            $authKey   = $this->app->config->cacheKey->get('MemberTokenKey', '') . $token;    // 用户认证key
            $memberIdToToken = $this->app->redisService->token->get($authKey);                // 取缓存中的用户访问token      
            
            if ( $memberIdToToken != $memberId ) {
                $message = sprintf('Request member_id = %s $token=%s cache token member_id = %s', $memberId, $token, $memberIdToToken);
                Logger::error($message, self::LOG_NAME);
                Response::responseJsonAndExit(Response::HTTP_STATUS_FAILED_USER_NO_LOGIN, '亲，您还未登陆，请登陆。');
            }
        }
    }
}
