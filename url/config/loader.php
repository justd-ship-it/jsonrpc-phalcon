<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
    [
        APP_PATH . '/Controller/'
    ]
);

$loader->registerNamespaces(
    [
        'ShortUrl\Lib' => '../src/Lib/',
        'ShortUrl\JsonRpc' => '../src/JsonRpc/',
    ]
);

$loader->register();
