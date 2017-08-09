<?php
error_reporting(E_ALL);

date_default_timezone_set('Asia/Shanghai');
define('APP_PATH', dirname(__DIR__) . '/');

// linux env
define('CONF_PATH', '/home/skin/conf/');
define('LOG_PATH', '/home/skin/log/');

define("IMA_UPLOAD_PATH", __DIR__.'/upload/images/');
define("VIDEO_UPLOAD_PATH", __DIR__.'/upload/video/');
define("AUDIO_UPLOAD_PATH", __DIR__.'/upload/audio/');

$di = new \Phalcon\Di\FactoryDefault();

include CONF_PATH . 'env.php';
include './init/loader.php';
include './init/services.php';

$app = new \Phalcon\Mvc\Application($di);

$app->useImplicitView(false);

$response = $di->getResponse();

$response ->setContentType('application/json', 'UTF-8');

try {
    $response = $app->handle();
} catch (App\Exception\AppException $e) {   
    if ($e instanceof App\Exception\ApiException) {
        $httpStatus = $e->getHttpStatus();
        
        $errcode = $di->getShared('errcode');
        $httpMessage = $errcode[$httpStatus];
        
        $response->setStatusCode($httpStatus, $httpMessage);//1.3.x bug: setStatusCode the second param must set
        $response->setJsonContent(array('errorCode' => $e->getCode(), 'errorMessage' => $e->getMessage()));
    }
    
    //must catch ServiceException in action, and throw ApiException if error
    
    if ($e instanceof App\Exception\GatewayException) {
        $di->getLogger()->log($e->getMessage());
        $response->setStatusCode(504, 'Gateway Time-out');
        $response->setJsonContent(array('errorCode' => 504, 'errorMessage' => 'Gateway Time-out'));
    }
    
} catch (Phalcon\Exception $e) {   
    $di->getLogger()->log($e->getMessage());
    $response->setStatusCode(500, 'Internal Server Error');
    $response->setJsonContent(array('errorCode' => 500, 'errorMessage' => 'Internal Server Error'));
    
} 

// ^ _ ^ < php5.5 not support finally
$response->send();
