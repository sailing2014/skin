<?php
use Phalcon\Config\Adapter\Php as Config;
use Phalcon\Logger\Adapter\File as Logger;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Dispatcher;
use App\Plugins\NotFoundPlugin;
use Helper\HttpRequest;
use Phalcon\Mvc\Router;


$di->set('router', function () {
    $router = new Router(false);
    $router->removeExtraSlashes(true);
    
    $router->setDefaultNamespace('App\Html\Controllers');         
    $router->setDefaultController('index');
    $router->setDefaultAction('index');
    
    $router->add("/html/article/index/{encyclopedia_id:\S{31,32}}", array(
                    'controller' => 'article',
                    'action' => 'index'                   
                ));
    $router->add("/html/product/index/{id:\S{13,26}}", array(
                    'controller' => 'product',
                    'action' => 'index'
                ));
     $router->add("/html/product/elementList/{id:\S{13,26}}", array(
                    'controller' => 'product',
                    'action' => 'elementList'
                ));
    $router->add("/html/product/element/{id:\S{24,36}}", array(
                    'controller' => 'product',
                    'action' => 'element'
                ));
    $router->add("/html/product/riskList", "product::riskList" );
    
    //for testers
    $router->add("/html/test/photos", "photo::index" );
    
    //for temporarily bbc
    $router->add("/html/test/bbc", "photo::bbc" );
    
    return $router;
}, true);

$di->set('logger', function () {
    $logger = new Logger(LOG_PATH . 'debug.log');
    return $logger;
}, true);

// NOTE: return array not object
$di->set('errcode', function () {
    $errcode = new Config(CONF_PATH . 'errcode.php');
    return $errcode->toArray();
}, true);

// NOTE: return array not object
$di->set('api', function () {
    $api = new Config(CONF_PATH . 'api.php');
    return $api->toArray();
}, true);

$di->set('upload', function () {
    $config = new Config(CONF_PATH . 'upload.php');
    return $config->toArray();
}, true);

$di->set('dispatcher', function () {
    $eventsManager = new EventsManager();
    
    /**
     * Handle exceptions and not-found exceptions using NotFoundPlugin
     */
    $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin());
    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});

$di->set('request', function () {
    return new HttpRequest();
}, true);

/**
 * set view 
 */
 $di -> set('view', function () {
            $view = new \Phalcon\Mvc\View();
            $view -> setViewsDir(APP_PATH.'/views/');
            $view->registerEngines(array(
                                        ".phtml" => 'Phalcon\Mvc\View\Engine\Volt')
                                   );
            return $view;
    });