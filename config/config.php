<?php
return array(
    'environment'  => 'local', // 服务器环境(local: 开发环境；test: 测试环境；production: 生产环境)
    'log'   => array(
        'dataSource' => array('file', 'thriftService'),
        'dataSource' => array('file'),
        'logFileDir' => APP_LOG_PATH
    ),
    'cacheKey'  => require 'cachekey.php',
    'router'        => require 'routers.php',
    'messages'      => require 'messages.php',
    'redisDrive'    => 'pecl_redis',   //Predis: 使用Predis 扩展包 pecl_redis: php扩展 默认使用php扩展
    'services'      => array(
        'thfirtService'     => 'App\Services\ThriftService',
        'redisService'      => 'App\Services\RedisService',
        'dbService'         => 'App\Services\DbService',
        'amqpService'       => 'App\Services\AMQPService',
    ),
    'events'       => array(
        'micro:beforeExecuteRoute' => 'App\Library\Events\beforeExecuteRouteEvent'
    ),
    'sodiumFilter' => array(
        'enable' => true,
        'filterRule'      => array(
            DEVICE_NAME_IPHONE  => '2.0.1',
            DEVICE_NAME_IPAD    => '2.0.1',
            DEVICE_NAME_ANDRIOD => '2.0.4',
        ),
        'serverSecretKey' => '70050e783ced42343079347d33b8154c576f29330eb9a25dd610b6dafef3bc4b',
        'serverPubKey'    => 'a6fac5cb60f64f886f74a7fb4601ee5068b04affc768048c6506faac757f8c41',
    ),
);