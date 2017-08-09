<?php
error_reporting(E_ALL);
date_default_timezone_set('UTC');
define('APP_PATH', dirname(__DIR__) . '/../');
// linux env
define('CONF_PATH', '/home/skin/conf/');
define('LOG_PATH', '/home/skin/log/');
define('ASSET_PATH','/home/skin/web/html/assets');

$di = new \Phalcon\Di\FactoryDefault();

include CONF_PATH . 'env.php';
include './init/loader.php';
include './init/services.php';

$app = new \Phalcon\Mvc\Application($di);

try {
    $response = $app -> handle() -> getContent();
    echo $response;
} catch (\Phalcon\Exception $e) {
    echo "PhalconException: ", $e -> getMessage();
}