<?php
/**
 * @author chenmiao(陈淼)<chenmiao2@tianhong.com>
 */
namespace App\Services;

use Phalcon\Di;
use App\Services\BaseService;
use App\Library\Log\Logger;

class AMQPService {
    
    /**
     * @var string 服务配置名
     */
    const CONFIG_KEY = 'amqp';
    /**
     * @var string AMQP 队列服务器默认主机ip
     */
    const AMQP_SERVER_HOST = '127.0.0.1';
    /**
     * @var string AMQP 队列服务器默认IP
     */    
    const AMQP_SERVER_PORT = 5672;
    /**
     * @var string AMQP 队列服务器默认用户名
     */        
    const AMQP_SERVER_USERNAME = '';
    /**
     * @var string AMQP 队列服务器默认密码
     */            
    const AMQP_SERVER_PASSWORD = '';
    /**
     * @var string AMQP 队列服务器默认主机
     */    
    const AMQP_SERVER_VHOST = '/';
    /**
     * @var int retry 次数
     */
    const CONN_RETRY_COUNT = 5;
    /**
     * @var \AMQPConnection $connection
     */
    public $connection;
    /**
     * 是否连接
     * 
     * @var Boolean $isConnected
     */
    private static $isConnected = FALSE;
    
    
    public function getInstance() {
       if( FALSE === self::$isConnected ) {
           $this->init()->connect();
       }
        return $this;
    }
    /**
     * AMQP 参数初始化
     * 
     * @return \App\Services\AMQPService
     */
    public function init() {
        $amqpConfig     = Di::getDefault()->getShared('config')->{self::CONFIG_KEY};     
        $credentials = array(
            'host'      => $amqpConfig->get('host', self::AMQP_SERVER_HOST),
            'port'      => $amqpConfig->get('port', self::AMQP_SERVER_PORT),
            'vhost'     => $amqpConfig->get('vhost', self::AMQP_SERVER_VHOST),
            'login'     => $amqpConfig->get('login', self::AMQP_SERVER_USERNAME),
            'password'  => $amqpConfig->get('password', self::AMQP_SERVER_PASSWORD)
        );        
        $this->connection = new \AMQPConnection($credentials);
        return $this;
    }

    /**
     * 连接mq服务器
     * 
     * @param int $retryCnt 重连次数
     * @return \App\Services\AMQPService
     * @throws \AMQPConnectionException
     */
    public function connect( $retryCnt = 0 ) {
        try {
            if( 0 === $retryCnt ) {
                $this->connection->connect();
            } else {
                $this->connection->reconnect();
            }
            if( !$this->connection->isConnected() ) {
                self::$isConnected = FALSE;
                $message = sprintf('Cannot connect to the broker, $retryCnt=' . $retryCnt);
                Logger::error($message, 'AMQPService');
                while ( $retryCnt < self::CONN_RETRY_COUNT ) {
                    $retryCnt ++;
                    $this->connect($retryCnt);
                }
            } else {
                self::$isConnected = TRUE;
            }
            if( FALSE === self::$isConnected ) {
                Logger::error('Connect to the broker Failed', 'AMQPService');    
            }
        } catch (\AMQPConnectionException $exception) {
            self::$isConnected = FALSE;
            if( $retryCnt < self::CONN_RETRY_COUNT  ) {
                $retryCnt ++;
                $message = sprintf('Cannot connect to the broker, $retryCnt=' . $retryCnt);
                Logger::error($message, 'AMQPService');
                $this->connect($retryCnt); 
            }
            Logger::error('Connect to the broker Failed', 'AMQPService');
            throw  new \AMQPConnectionException;
        }
        return $this;
    }
    
    
}
