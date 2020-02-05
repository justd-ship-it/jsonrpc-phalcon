<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Url;

/**
 * The FactoryDefault Dependency Injector automatically registers the right
 * services to provide a full stack framework
 */
$di = new FactoryDefault();

/**
 * Database connection is created based on the parameters defined in the
 * configuration file
 */
$di->set('db', function () use ($config) {
    return new \Phalcon\Db\Adapter\Pdo\Postgresql([
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname,
    ]);
});

$di->set(
    'dispatcher',
    function () {
        // Create an event manager
        $eventsManager = new \Phalcon\Events\Manager();

        $dispatcher = new \Phalcon\Mvc\Dispatcher();

        // Bind the eventsManager to the view component
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    },
    true
);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$di->set(\ShortUrl\Lib\HashId::class, function () use ($config) {
    $hashId = new \ShortUrl\Lib\HashId();
    $hashId->setKey($config->hashId->key);
    return $hashId;
});

$di->set('router', function () use ($requestBody) {
    return new \ShortUrl\JsonRpc\Router();
});

$di->set('view', function () {
    $view = new Phalcon\Mvc\View();
    $view->disable();
    return $view;
});

