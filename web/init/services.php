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
    $router->setDefaultNamespace('App\Controllers');
    
    // router not found
    $router->notFound(array('controller' => 'index', 'action' => 'route404'));
    
    // get router prefix
    $uri = $router->getRewriteUri();
    $uris = explode('/', trim($uri, '/'), 3);
   
    if (count($uris) >= 2) {
        $prefix = '/' . implode('/', array_slice($uris, 0, 2));      
        // read router config
        $routers = include CONF_PATH . 'routers.php';
        if (array_key_exists($prefix, $routers)) {
            $subRouters = $routers[$prefix];            
            foreach ($subRouters as $sub) {
                $router->add($prefix . $sub[0], $sub[1], $sub[2]);
            }
        }
    }
    
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