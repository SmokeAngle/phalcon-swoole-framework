<?php
define('APP_ROOT', realpath(dirname('../..')));
define('DS', DIRECTORY_SEPARATOR);
define('APP_CONFIG_PATH', APP_ROOT . DS .  'config');
define('APP_LOG_PATH', APP_ROOT .  DS . 'storages' . DS . 'logs');
define('APP_DEFAULT_TIME_ZONE', 'Asia/Shanghai');
define('DEBUG', TRUE);
define('APP_EXCEPTION', 'application.exception');

define('DEVICE_NAME_IPHONE', 'iphone');             //IPHONE 设备类型名
define('DEVICE_NAME_IPAD', 'ipad');                 //IPAD 设备类型名
define('DEVICE_NAME_ANDRIOD', 'android');           //ANDRIOD 设备类型名
define('DEVICE_NAME_ANDRIODPAD', 'android_pad');    //ANDRIOD 设备类型名
define('DEVICE_NAME_ANDRIODTV', 'android_tv');      //ANDRIOD 设备类型名
define('DEVICE_NAME_WAP', 'wap');                   //wap 设备类型名
define('DEVICE_NAME_UNKOWN', 'unkown');             //wap 设备类型名