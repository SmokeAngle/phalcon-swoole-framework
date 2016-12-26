<?php
/**
 * 天虹商场股份有限公司版权所有
 *
 * 陈淼
 * 2016.2.22
 */
namespace App\Services\Thrift;

use Thrift\Transport\TSocketPool;
use App\Library\Log;

/**
 * Class BaseService
 * @package Services
 *
 * THBase客户端基础类
 */
class BaseService
{

    /**
     * @var string 默认主机
     */
    protected $defaultHost = '192.168.148.31';

    /**
     * @var string 默认端口
     */
    protected $defaultPort= 9292;

    /**
     * @var object 客户端对象
     * @example
     * $this->client = new \THBaseMemberClient($this->protocol, $this->protocol);
     */
    public $client;
    
    /**
     * @var string protocol 类名，包含命名空间
     */
    protected $protocolClass = 'Thrift\\Protocol\\TBinaryProtocol';
    /**
     * @var string transport 类名，包含命名空间
     */
    protected $transportClass = 'Thrift\\Transport\\TBufferedTransport';

    /**
     * @var Thrift\Transport\TBufferedTransport
     */
    protected $transport;

    /**
     * @var Thrift\Protocol\TBinaryProtocol
     */
    protected $protocol;

    /**
     * @var string 主机名
     */
    public $host;

    /**
     * @var int 端口号
     */
    public $port;
    
    /**
     * @var obejct Phalcon\Validation
     */
    public $validation ;
    
    /**
     * @var int 链接失败时，尝试重连次数
     */
    public $connTry = 10;

    public static $thriftInit = false;
    
    /**
     * construct（构造函数）
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->host = isset($config['host']) ? $config['host'] : $this->defaultHost;
        $this->port = isset($config['port']) ? $config['port'] : $this->defaultPort;
        
        if( is_string($this->host) ) {
            $this->host = [ $this->host ];
        }
        if(is_string($this->port) ) {
            $this->port = [ $this->port ];
        }
        if( is_object($this->host) ) {
            $this->host = get_object_vars($this->host);
        }
        if( is_object($this->port) ) {
            $this->port = get_object_vars($this->port);
        }
        $this->startTransport();
    }
    
    /**
     * 连接并打开执行事务
     */
    public function startTransport($tryCount = 0)
    {
        try {
            $socket = new TSocketPool($this->host, $this->port);
            $socket->setSendTimeout(15000);
            $socket->setRecvTimeout(15000);
            $this->transport = new $this->transportClass($socket);
            $this->protocol = new $this->protocolClass($this->transport);
            $this->transport->open();
        } catch (\Thrift\Exception\TTransportException $tx) {
            Log::info('11代码行数:' . __LINE__ . '错误信息：' . var_export($tx, true), basename(__FILE__, '.php'));
            if ($tryCount <= $this->connTry) {
                $this->startTransport($tryCount + 1);
            } else {
                Log::info('22代码行数:' . __LINE__ . '错误信息：' . var_export($tx, true), basename(__FILE__, '.php'));
                throw new \Exception($tx->getMessage(), $tx->getCode());
            }
        }
    }
    
    /**
     * 验证参数的有效性
     *
     * @param array $data
     * @return
     */
    public function validate($data)
    {
        $messages  = $this->validation->validate($data);
        if (count($messages)) {
            foreach ($messages as $message) {
                return $this->result(array(), 40002, $message->getMessage());
            }
        }
        return true;
    }
    /**
     * 关闭当前连接
     */
    public function close()
    {
        $this->transport->close();
    }


    /**
     * __destruct(析构函数)
     */
    public function __destruct()
    {
        $this->close();
    }
}
