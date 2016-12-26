<?php
/**
 * @author chenmiao(陈淼)<382169722@qq.com>
 */
require_once realpath(dirname('../..')) . '/config/constant.php';
require_once APP_ROOT . '/config/loader.php';


use Phalcon\Mvc\Micro;

date_default_timezone_set(APP_DEFAULT_TIME_ZONE);

$httpServer = new swoole_http_server("0.0.0.0", 9501);
$application = new Micro();

$httpServer->set(array(
    'worker_num' => 4,  //base on you cpu nums 
    'task_worker_num' => 8, //better equal to worker_num, anyway you can define your own 
    'heartbeat_check_interval' => 5, 
    'heartbeat_idle_time' => 5, 
    'open_cpu_affinity' => 1, 
    'open_eof_check'  => 1, 
    'package_eof'   => "\r\n\r\n", 
    'package_max_length' => 1024 * 16, 
    'daemonize' => 1
));

$httpServer->on('request', function (swoole_http_request $request, swoole_http_response $response) use ( $httpServer ) {
    $requestUri = $request->server['request_uri'];
    $requestParms = isset($request->get) ? $request->get : array(); 
    
    if( $requestUri === '/async/state' ) {
        $serverStats = $httpServer->stats();
        $response->end(json_encode($serverStats));
    } else {
        $taskData = array_merge($requestParms, array(
            'type'      => $requestUri,
            'create_at' => date('Y-m-d H:i:s')
        )); 
        $retData = json_encode(array('ret' => 200, 'message' => 'success'));
        $httpServer->task($taskData);
        $response->end($retData);
    }
});

$httpServer->on('Task', function(swoole_http_server $httpServer, $taskId, $fromId, $taskData ) use( $application, $routerMap ) {
    //@todo 异步逻辑
    App\Library\Log\Log::info("DATA ERROR TASK_ID=$taskId FROM_ID=$fromId:" . json_encode($taskData), 'reportAsyncServer');
    $httpServer->finish($taskData); 
});

$httpServer->on('Finish', function( $httpServer, $data ) {
    App\Library\Log\Log::info('onFinish', 'reportAsyncServer');
});

$httpServer->start();