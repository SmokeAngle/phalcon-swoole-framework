<?php
/**
 * @author chenmiao
 * @version 1.1.0
 */
namespace App\Services;

use Phalcon\Di;
//use App\Library\Common\Mysql;
use App\Services\BaseService;
use Phalcon\Db\Adapter\Pdo\Mysql;

/**
 * @property \Phalcon\Db\Adapter\Pdo\Mysql $default 默认连接
 */

class DbService extends BaseService {
    
    public static $serviceNamePrefix = 'mysql_service_';
    public static $serviceDiNamePrefix = 'di_mysql_service_';

    public $connServiceNames = array();


    public function __get( $name ) {
        $serviceName = sprintf('%s%s', self::$serviceNamePrefix, $name);
        $diServiceName = sprintf('%s%s', self::$serviceDiNamePrefix, $name);
        if( FALSE === ( $dbService = $this->getService($serviceName) ) ) { 
            $dbConfig = Di::getDefault()->getConfig()->databases;
            if( !empty($dbConfig->{$name}) ) { 
                $dbService = new Mysql($dbConfig->{$name}->toArray());
                $this->addService($serviceName, $dbService);
                $this->connServiceNames[$name] = $diServiceName;
                Di::getDefault()->setShared($diServiceName, $dbService);
            }
        }
        return $dbService;
    }
    
    /**
     * 返回mysql注册服务名
     * 
     * @param string $name dbService 名称
     * @return mixed
     */
    public function getConnectionServiceName( $serviceName ) {
        $name = str_replace(self::$serviceNamePrefix, '', $serviceName);
        $diServiceName = sprintf('%s%s', self::$serviceDiNamePrefix, $name);
        if(array_key_exists($name, $this->connServiceNames) ) {
            $diServiceName = $this->connServiceNames[$name];
        } else {
            if( FALSE === ( $dbService = $this->getService($serviceName) ) ) { 
                $dbConfig = Di::getDefault()->getConfig()->databases;
                if( !empty($dbConfig->{$name}) ) { 
                    $dbService = new Mysql($dbConfig->{$name}->toArray());
                    $this->addService($serviceName, $dbService);
                    $connServiceName = $this->connServiceNames[$name] = $serviceName;
                    Di::getDefault()->setShared($diServiceName, $dbService);
                }
            }
        }
        return $diServiceName;
    }
    
}
