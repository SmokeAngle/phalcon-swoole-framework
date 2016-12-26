<?php
/**
 * App Category | app/Application.php
 *
 * @package     self-checkout
 * @subpackage  Core
 * @author      chenmiao<陈淼><382169722@qq.com>
 * @version     v.1.0.1 (06/12/2016)
 * @copyright   Copyright (c) 2016, honglingjin.cn
 */
namespace App;

use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Config;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use App\Library\Log\Logger;
use App\Library\Common\Response;


/**
 * 框架核心入口
 */
class Application {
    /**
     * 日志名
     * 
     * @var string 
     */
    const LOG_NAME = 'application';
    /**
     * HTTP GET请求方式
     * 
     * @var string 
     */
    const HTTP_METHOD_GET = 'get';
    /**
     * HTTP POST请求方式
     * 
     * @var string 
     */
    const HTTP_METHOD_POST = 'post';
    /**
     * @var \Phalcon\Mvc\Micro 
     */
    public static $app;
    /**
     * @var \Phalcon\Di\FactoryDefault
     */
    public static $di;
    /**
     * @var array 
     */
    public $services = array();
    
    /**
     * @var string 生产环境
     */
    const APP_ENV_PRODUCTION = 'production';
    /**
     * @var string 测试环境
     */
    const APP_ENV_TEST = 'test';
    /**
     * @var string 开发环境
     */
    const APP_ENV_LOCAL = 'local';
    /**
     * @var string 当前运行环境
     */
    public static $env;
    /**
     * 运行框架
     */
    public function run( ) {
        $this->regsiterDi()->regsiterEnvConfig()->regsiterApp()->regsiterRouter()->regsiterEvent();
        $envConfig = self::$di->getShared('config');
        if(  FALSE !== ( $userServices = ( $envConfig->get('services', FALSE) ) ) ) {
            $this->setServices($userServices);
        } 
        self::$app->handle();
    }
    /**
     * 注册全局App
     * 
     * @return \App\Application
     */
    private function regsiterApp() {
        self::$app = new Micro(self::$di); 
        return $this;
    }

    /**
     * 注册全局配置
     * 
     * @return \App\Application
     */
    private function regsiterEnvConfig() {
        self::$di->setShared('config', function() {
                $configFile = APP_CONFIG_PATH . DS . 'config.php';
                $configArr = array();
                if(is_readable($configFile) ) {
                    $configArr = require_once $configFile;
                    $config = new Config($configArr);
                }
                $env = $config->get('environment', 'local');
                self::$env = $env;
                $envConfigDir = APP_CONFIG_PATH . DS . $env;
                if(is_dir($envConfigDir)) {
                    $dirHandler = opendir($envConfigDir);
                    while (FALSE !== ( $fileName = readdir($dirHandler) )) {
                        $currentFile = $envConfigDir . DS . $fileName;
                        if(( '.' !== $fileName )  && ('..' !== $fileName) && is_readable($currentFile) ) {
                            $currentEnvConfigArr = require_once $currentFile;
                            $envConfig = new Config($currentEnvConfigArr);
                            if(!empty($config)) {
                                $config->merge($envConfig);
                            } else {
                                $config = $envConfig;
                            }
                        }
                    }
                }
                return $config;
        });
        return $this;
    }
    
    /**
     * 注册路由配置
     * 
     * @return void
     */
    private function regsiterRouter() {
        
        $routers = self::$app->config->router;
        foreach ( $routers as $routerName => $router ) {
            $routerArr = $router->toArray();
            $url = !empty($routerArr['url']) ? $routerArr['url'] : '';
            $controller = !empty($routerArr['controller']) ? $routerArr['controller'] : '';
            $action = !empty($routerArr['action']) ? $routerArr['action'] : '';
            $httpMethod = !empty($routerArr['method']) ? $routerArr['method'] : self::HTTP_METHOD_GET;
            if( !empty($url) && !empty($controller) && !empty($action) ) {
                switch ( $httpMethod ) {
                    case self::HTTP_METHOD_POST:
                        self::$app->post($url, function() use($controller, $action )  {
                            return ( new $controller )->{$action}();
                        })->setName($routerName);
                        break;
                    case self::HTTP_METHOD_GET:
                    default :
                        self::$app->get($url, function( ) use($controller, $action) {
                            return ( new $controller )->{$action}();
                        })->setName($routerName);
                }
            }
        }
        
        
        self::$app->notFound(function() {
            return Response::responseJson(Response::HTTP_STATUS_PAGE_NOT_FOUND, 'api not found');
        });
        return $this;
    } 
    
    /**
     * 注册全局Di
     * 
     * @return \App\Application
     */
    private function regsiterDi() {
        self::$di = new FactoryDefault();
        return $this;
    }   
    
    /**
     * 注册用户自定义事件
     * 
     * @return \App\Application
     */
    private function regsiterEvent() {
        if( FALSE !== ( $events = self::$app->config->get('events') ) ) {
            $eventsManager = new EventsManager();
            $dispatcher = self::$app->getSharedService('dispatcher');                        
            foreach ( $events as $eventType => $eventClass ) {
                $eventsManager->attach($eventType, function( Event $event, Micro $app ) use( $eventClass, $dispatcher,$eventType ) {
                    return (new $eventClass($event, $app))->execute();
                });
            }
            self::$app->setEventsManager($eventsManager);
        }
        return $this;
    }


    /**
     * 注册配置服务
     * 
     * @param object $serviceConfig
     */
    public function setServices( $serviceConfig ) {
        foreach ( $serviceConfig as $serviceName => $serviceClass ) {
            if( class_exists($serviceClass) ) {
                self::$di->setShared($serviceName, function() use( $serviceClass ) {
                    return new $serviceClass;
                });
            } else {
                $message = sprintf( '[setServices] $serviceName = %s $serviceClass = %s register fail', $serviceName, $serviceClass);
                Logger::warning($message, self::LOG_NAME);
            }
        }
    }
}

