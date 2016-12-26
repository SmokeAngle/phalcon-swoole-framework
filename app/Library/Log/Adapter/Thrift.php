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

namespace App\Library\Log\Adapter;

use \Phalcon\Di;
use Phalcon\Logger;

/**
 * Thrift 日志Adapter
 */
class Thrift extends \Phalcon\Logger\Adapter implements \Phalcon\Logger\AdapterInterface
{
    
    
    public $module = '';
    
    /**
     *
     * @var object \Services\Log\LogService;
     */
    public $client;

    /**
     *  constructor
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name, $options = null)
    {
        $this->module = $name;
        $this->client = Di::getDefault()->get('thbaseService')->LogService;
    }
    
    /**
     * Returns the internal formatter
     *
     * @return \Phalcon\Logger\FormatterInterface
     */
    public function getFormatter()
    {
    }
    
    /**
     * Writes the log to the stream itself
     *
     * @param string $message
     * @param int $type
     * @param int $time
     * @param array $context
     */
    public function logInternal($message, $type, $time, $context)
    {
//        var_dump($type);
        switch ($type) {
            case Logger::CRITICAL:
            case Logger::ALERT:
            case Logger::ERROR:
                $this->client->error($message, $this->module);
                break;
            case Logger::WARNING:
                $this->client->warn($message, $this->module);
                break;
            case Logger::DEBUG:
                $this->client->debug($message, $this->module);
                break;
            case Logger::CUSTOM:
            case Logger::SPECIAL:
            case Logger::INFO:
                $this->client->info($message, $this->module);
                break;
            default:
                $this->client->info($message, $this->module);
        }
    }
    
    /**
     * 获取格式化日志内容
     *
     * @param string $message
     * @return string
     */
    public function getFormatMessage($message)
    {
        $logMessage = $this->getFormatMessage($message);
        return $logMessage;
    }


    /**
     * Closes the logger
     *
     * @return bool
     */
    public function close()
    {
        $this->client->close();
    }
}
