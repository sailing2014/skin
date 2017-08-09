<?php

$loader = new \Phalcon\Loader();
/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces(
        array(
            'App\Html\Controllers' => APP_PATH . 'controllers/html',
            'App\Models' => APP_PATH . 'models/',
            'App\Plugins' => APP_PATH . 'plugins/',
            'App\Exception' => APP_PATH . 'exceptions/'
        )
);

$loader->registerDirs(
        array(
            APP_PATH . 'lib/'
        )
);

$loader->register();