<?php
/**
 * @author chenmiao
 * @version 1.1.0
 */
namespace App\Services;

use App\Services\BaseService;

/**
 * @property \App\Services\Thrift\Member\MemberService $MemberService 会员服务
 */
class ThriftService extends BaseService
{
    
    public function __get($name)
    {
        $subNameSpace = substr($name, 0, -7);
        $className = __NAMESPACE__ . "\\Thrift\\$subNameSpace\\$name";
        
        $thriftConfig = \Phalcon\Di::getDefault()->getConfig()->thrift;
        $serverConf = isset($thriftConfig->{strtolower($subNameSpace)}) ? get_object_vars($thriftConfig->{strtolower($subNameSpace)}) : array();
        if (class_exists($className)) {
            if (false === (  $currentService = $this->getService($name) )) {
                $currentService = new $className($serverConf);
                $this->addService($name, $currentService);
            }
            return $currentService;
        }
        return null;
    }
}
