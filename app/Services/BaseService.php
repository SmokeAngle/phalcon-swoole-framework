<?php
/**
 * @author chenmiao
 * @version 1.1.0
 */
namespace App\Services;

class BaseService
{
    
    public $services = [];
    
    /**
     * 新增服务
     *
     * @param string $className
     * @param Services\BaseService $object
     * @return void
     */
    public function addService($className, $object)
    {
        if (!array_key_exists($className, $this->services)) {
            $this->services[$className] = $object;
        }
    }
    
    /**
     * 获取当前服务
     *
     * @param string $className
     * @return boolean|Services\BaseService
     */
    public function getService($className)
    {
        if (array_key_exists($className, $this->services)) {
            return $this->services[$className];
        }
        return false;
    }
}
