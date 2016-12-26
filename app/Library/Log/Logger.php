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

namespace App\Library\Log;

use Phalcon\Logger\Multiple as MultipleStream;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Di;
use App\Library\Log\Adapter\Thrift as ThriftAdapter;

/**
 * 通用日志类
 */
class Logger
{
    
    /**
     * @var string 文本日志
     */
    const LOG_DATA_SOURCE_FILE = 'file';
    /**
     * @var string 服务端日志
     */
    const LOG_DATA_SOURCE_THRIFT_SERVICE = 'thriftService';
    
    /**
     * @var bolean 判断Thrift客户端是否有效
     */
    public static $isValidThriftAdapter = true;

    /**
     * @var array
     */
    private static $loggers = [];
    
    private static function getLogger($module = "phalcon")
    {
        if (isset(self::$loggers[$module])) {
            return self::$loggers[$module];
        }
        $multipleStreamLogger = new MultipleStream();
        $config = Di::getDefault()->getConfig();
        $dataSource = $config->log->dataSource;
        if (empty($dataSource)  || ( !is_array($dataSource) && !is_object($dataSource) )) {
            throw new \Exception('日志源配置错误', 6000);
        }
        foreach ($dataSource as $dataSourceItem) {
            switch ($dataSourceItem) {
                case self::LOG_DATA_SOURCE_FILE:
                    $logPath = ( isset($config->logFileDir) ? $config->logFileDir : APP_ROOT . DS . 'storages/logs/' ) . date('Ymd') . '/';
                    if (!is_dir($logPath)) {
                        mkdir($logPath, 0777, true);
                    }
                    $logFilePath = sprintf('%s%s.%s', $logPath, $module, 'log');
                    $fileLogger = new FileAdapter($logFilePath);
                    $multipleStreamLogger->push($fileLogger);
                    break;
                case self::LOG_DATA_SOURCE_THRIFT_SERVICE:
                    if (self::$isValidThriftAdapter) {
                        try {
                            $thriftLogger = new ThriftAdapter($module);
                            $multipleStreamLogger->push($thriftLogger);
                        } catch (\Exception $ex) {
                            self::$isValidThriftAdapter = false;
                            Log::error($ex->getMessage(), APP_EXCEPTION);
                            Log::error($ex->getTraceAsString(), APP_EXCEPTION);
                        }
                    }
                    break;
            }
        }
        self::$loggers[$module] = $multipleStreamLogger;
        return self::$loggers[$module];
    }
    
    /**
     * Sends/Writes an alert message to the log
     *
     * @param string $message   日志信息
     * @param string $module    日志模块
     * @return void
     */
    public static function alert($message = "", $module = "phalcon")
    {
        $logMessage = self::getLogMessageFormat($message);
        self::getLogger($module)->alert($logMessage);
    }
    /**
     * Sends/Writes an info message to the log
     *
     * @param string $message   日志信息
     * @param string $module    日志模块
     * @return void
     */
    public static function info($message = "", $module = "phalcon")
    {
        $logMessage = self::getLogMessageFormat($message);
        self::getLogger($module)->info($logMessage);
    }
    /**
     * Sends/Writes an notice message to the log
     *
     * @param string $message   日志信息
     * @param string $module    日志模块
     * @return void
     */
    public static function notice($message = "", $module = "phalcon")
    {
        $logMessage = self::getLogMessageFormat($message);
        self::getLogger($module)->notice($logMessage);
    }
    /**
     * Sends/Writes an debug message to the log
     *
     * @param string $message   日志信息
     * @param string $module    日志模块
     * @return void
     */
    public static function debug($message = "", $module = "phalcon")
    {
        $logMessage = self::getLogMessageFormat($message);
        self::getLogger($module)->debug($logMessage);
    }
    /**
     * Sends/Writes an warning message to the log
     *
     * @param string $message   日志信息
     * @param string $module    日志模块
     * @return void
     */
    public static function warning($message = "", $module = "phalcon")
    {
        $logMessage = self::getLogMessageFormat($message);
        self::getLogger($module)->warning($logMessage);
    }
     /**
     * Sends/Writes an critical message to the log
     *
     * @param string $message   日志信息
     * @param string $module    日志模块
     * @return void
     */
    public static function critical($message = "", $module = "phalcon")
    {
        $logMessage = self::getLogMessageFormat($message);
        self::getLogger($module)->critical($logMessage);
    }
     /**
     * Sends/Writes an error message to the log
     *
     * @param string $message   日志信息
     * @param string $module    日志模块
     * @return void
     */
    public static function error($message = "", $module = "phalcon")
    {
        $logMessage = self::getLogMessageFormat($message);
        self::getLogger($module)->error($logMessage);
    }
    
    /**
     * 获取格式化日志
     *
     * @return string
     */
    public static function getLogMessageFormat($message = "")
    {
        $logMessage = $message;
        return $logMessage;
    }
}
