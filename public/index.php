<?php
use Phalcon\Mvc\Micro;
use App\Application;
use App\Library\Common\Response;

require_once realpath(dirname('../..')) . '/config/constant.php';
require_once APP_ROOT . '/config/loader.php';


if( TRUE === DEBUG ) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
date_default_timezone_set(APP_DEFAULT_TIME_ZONE);

function catchException($exception)
{
    $data = array(
        'code' => 5000,
        'message' => "服务器数据异常"
    );
     
    if( TRUE === DEBUG ) {
        $data['detail'] = array(
            'exCode'   => $exception->getCode(),
            'url'      => ( array_key_exists('_url', $_GET) ? $_GET['_url'] : '/' ) ,
            'errorMsg' => $exception->getMessage(),
            'trace'    => $exception->getTrace() 
        );
    }
    
    $logDetail  = "Url:" . ( array_key_exists('_url', $_GET) ? $_GET['_url'] : '/' ) . "<br />\r\n";
    $logDetail .= "Message:" .$exception->getMessage() . "<br />\r\n";
    $logDetail .= "Trace:" . $exception->getTraceAsString();
    App\Library\Log\Logger::error($logDetail, APP_EXCEPTION);
    exit(json_encode($data));
}

function catchError($errno, $errstr, $errfile, $errline)
{

    $data = array(
        'code' => 5001,
        'message' => "服务器数据异常"
    );
    if( TRUE === DEBUG ) {
        $data['detail'] = array(
            'exCode'   => $errno,
            'url'      => ( array_key_exists('_url', $_GET) ? $_GET['_url'] : '/' ),
            'errfile'  => $errfile,
            'errline'  => $errline,
            'trace'    => $errstr
        );
    }
    
    $logDetail  = "Url:" . ( array_key_exists('_url', $_GET) ? $_GET['_url'] : '/' ) . "<br />\r\n";
    $logDetail  = "Errno:" . $errno . "<br />\r\n";
    $logDetail .= "Errfile:" .$errfile . "<br />\r\n";
    $logDetail .= "Errline:" .$errline. "<br />\r\n";
    $logDetail .= "Errstr:" . $errstr;
    App\Library\Log\Logger::error($logDetail, APP_EXCEPTION);
    
    if ($errno !== E_WARNING &&  $errno !== E_NOTICE && $errno !== E_COMPILE_WARNING && $errno !== E_USER_WARNING &&
        $errno !== E_USER_NOTICE && $errno !== E_DEPRECATED && $errno !== E_USER_DEPRECATED) {
        exit(json_encode($data));
    } elseif( DEBUG ) {
        exit(json_encode($data));
    }
}

set_exception_handler('catchException');
set_error_handler('catchError');

(new Application())->run();
