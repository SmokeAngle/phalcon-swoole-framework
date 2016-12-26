<?php
/**
 * @author chenmiao(陈淼)
 * @version 1.0.0
 */
namespace App\Services\Thrift\Member;

use App\Services\Thrift\BaseService;
use App\Services\Thrift\Member\Validation\MemberValidation;
use Thrift\Client\Member\THBaseMemberClient;

class MemberBaseService extends BaseService
{
    /**
     * @var string 默认主机
     */
    protected $defaultHost = '192.168.148.31';

    /**
     * @var string 默认端口
     */
    protected $defaultPort= 9292;
    
    public function __construct($config = array())
    {
        parent::__construct($config);
        
        try {
            $this->client = new THBaseMemberClient($this->protocol, $this->protocol);
        } catch (Exception $exc) {
            Log::info('代码行数:' . __LINE__ . '错误信息：' . $exc->getMessage() . '错误编码: ' . $exc->getCode(), basename(__FILE__, '.php'));
            throw new \Exception($exc->getMessage(), $exc->getCode());
        }

        $this->validation = new MemberValidation();
        
    }
}
