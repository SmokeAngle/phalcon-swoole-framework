<?php
/**
 * @author chenmiao
 * @version 1.1.0
 */
namespace App\Services;

use Phalcon\Di;
use App\Services\BaseService;
use App\Library\Log\Logger;

/**
 * @property \Redis $common 主配置
 * @property \Redis $token 用户token验证
 */

class RedisService extends BaseService {
    
    const serviceNamePrefix = 'redis_service_';
    
    const LOG_NAME = 'RedisService';
    /**
     * @var string cluster 客户端
     */
    const REDIS_CLIENT_CLUSTER = 'cluster';
    /**
     * @var string replication 客户端
     */
    const REDIS_CLIENT_REPLICATION = 'replication';
    /**
     * @var string standalone 客户端
     */
    const REDIS_CLIENT_STANDALONE = 'standalone';
    /**
     *Predis模块是否初始化
     * 
     * @var Bollean $predisInit
     */
    public static $predisInit = false;
    
    /**
     * @var string predis驱动类型
     */
    const REDIS_DRIVE_TYPE_PREDIS = 'predis';
    /**
     * @var string php redis扩展驱动类型
     */
    const REDIS_DRIVE_TYPE_PECL_REDIS = 'pecl_redis';

    const REDIS_MASTER_ROLE_NAME = 'master';
    
    const REDIS_SLAVE_ROLE_NAME = 'slave';
    
    const REDIS_CONN_TIME_OUT = 5;
    
    const REDIS_WR_TIME_OUT = 5;

    private static $driveType;

    public function __construct() {
        $driveType = Di::getDefault()->getShared('config')->get('redisDrive', self::REDIS_DRIVE_TYPE_PECL_REDIS);        
        self::$driveType = strtolower($driveType);
    }

    public function __get($name) {
        $redisClient = FALSE;
        $serviceName = sprintf('%s%s_%s', self::serviceNamePrefix, self::$driveType, $name);
        
        if( self::REDIS_DRIVE_TYPE_PREDIS === self::$driveType ) {
            $redisClient = $this->getPredisClient($name, $serviceName);
        } elseif ( self::REDIS_DRIVE_TYPE_PECL_REDIS === self::$driveType ) {
            $redisClient = $this->getPeclRedisClient($name, $serviceName);
        } else {
            $redisClient = $this->getPeclRedisClient($name, $serviceName);
        }
        return $redisClient;
    }
    /** 
     * @return \Predis\Client
     */
    private function getPredisClient( $name, $serviceName ) {
        if( !self::$predisInit || !class_exists('\Predis\Client') ) {
            \Predis\Autoloader::register();
            self::$predisInit = TRUE;
        }
        if( FALSE === ( $redisService = $this->getService($serviceName) ) ) {
            $redisConfig = Di::getDefault()->getConfig()->redis;
            if( isset($redisConfig->{$name}) && isset($redisConfig->{$name}->type) && isset($redisConfig->{$name}->servers) ) {
                $type = strtolower($redisConfig->{$name}->type);
                $servers = get_object_vars($redisConfig->{$name}->servers);
                switch ( $type ) {
                    case self::REDIS_CLIENT_CLUSTER:
                        $redisService = new \Predis\Client($servers, [ 'cluster' => 'redis' ]);   
                        break;
                    case self::REDIS_CLIENT_REPLICATION:
                        $redisService = new \Predis\Client($servers, [ 'replication' => TRUE ]);
                        break;
                    case self::REDIS_CLIENT_STANDALONE:
                        $redisService = new \Predis\Client($servers);
                        break;
                }
            }
            if( !empty($redisService) ) {
                $this->addService($serviceName, $redisService);
            }
        }
        return $redisService;
    }
    
    private  function getPeclRedisClient( $name, $serviceName ) {
        if( FALSE === ( $redisService = $this->getService($serviceName) ) ) {
            $redisConfig = Di::getDefault()->getConfig()->redis;
            if( isset($redisConfig->{$name}) && isset($redisConfig->{$name}->type) && isset($redisConfig->{$name}->servers) ) {
                $type = strtolower($redisConfig->{$name}->type);
                $serversConfig = get_object_vars($redisConfig->{$name}->servers);
                switch ( $type ) {
                    case self::REDIS_CLIENT_CLUSTER:
                        if( class_exists('\RedisCluster') ) {
                            $redisClusterConfig = array_map(function( $currentItem ) {
                                $connArr = parse_url($currentItem);
                                return $connArr['host'] . ':' . $connArr['port'];
                            }, $serversConfig);
                            $redisService = new \RedisCluster(NULL, $redisClusterConfig, self::REDIS_CONN_TIME_OUT, self::REDIS_WR_TIME_OUT, TRUE);
                            $redisService->setOption(\RedisCluster::OPT_SLAVE_FAILOVER, \RedisCluster::FAILOVER_ERROR);   
                        } else {
                            $redisService = $this->getPredisClient($name, $serviceName);
                            $message = "Current redis extension version < 2.2.8 or redis extension not enable, use Predis";
                            Logger::warning($message, self::LOG_NAME);
                        }
                        break;
                    case self::REDIS_CLIENT_STANDALONE:
                        $redisServerConnStr = array_pop($serversConfig);
                        $redisServerConfig = $this->parseConStr($redisServerConnStr);
                        if( !empty($redisServerConfig['host']) && !empty($redisServerConfig['port'])) {
                            $redisService = new \Redis();
                            $redisService->connect($redisServerConfig['host'], $redisServerConfig['port'], self::REDIS_CONN_TIME_OUT);
                            if( !empty($redisServerConfig['database']) ) {
                                $redisService->select(intval($redisServerConfig['database']));
                            }
                        }   
                        break;
                }
            }
            if( !empty($redisService) ) {
                $this->addService($serviceName, $redisService);
            }
        }
        return $redisService;
    }
    
    public function parseConStr( $connStr ) {
        $connArr = parse_url($connStr);
        $parms = array(
            'host'  => !empty($connArr['host']) ? $connArr['host'] : '',
            'port'  => !empty($connArr['port']) ? $connArr['port'] : '',
        );
        $query = !empty($connArr['query']) ? $connArr['query'] : '';
        if( !empty($query) ) {
            $queryArr = explode('&', $query);
            while ( $currentItem = current($queryArr) ) {
                list($name, $value) = explode('=', $currentItem);
                $parms[$name] = $value;
                if( 'alias' === $name && FALSE !== strpos($value, self::REDIS_MASTER_ROLE_NAME) ) {
                    $parms['role'] = self::REDIS_MASTER_ROLE_NAME;
                }
                if( 'alias' === $name && FALSE !== strpos($value, self::REDIS_SLAVE_ROLE_NAME ) ) {
                    $parms['role'] = self::REDIS_SLAVE_ROLE_NAME;
                }
                next($queryArr);
            }      
        }
        return $parms;
    }
    
}
