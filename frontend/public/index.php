<?php

namespace Frontend;

use Frontend\Models\JsonRpc;
use Phalcon\DI;
use Phalcon\Escaper;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Application as BaseApplication;
use Phalcon\Url;

class Application extends BaseApplication
{
    protected function registerAutoloaders()
    {
        $loader = new Loader();

        $loader->registerNamespaces(
            [
                'Frontend\Controllers' => '../apps/controllers/',
                'Frontend\Models' => '../apps/models/'
            ]
        );

        $loader->register();
    }

    /**
     * This methods registers the services to be used by the application
     */
    protected function registerServices()
    {
        $di = new DI();

        // Registering a router
        $di->set('router', function () {
            return new Router();
        });

        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('Frontend\Controllers\\');

            return $dispatcher;
        });

        $di->set('url',
            function () {
                $url = new Url();
                $url->setBaseUri('/');
                return $url;
            }
        );

        $di->set('escaper', function () {
            return new Escaper();
        });

        // Registering a Http\Response
        $di->set('response', function () {
            return new Response();
        });

        // Registering a Http\Request
        $di->set('request', function () {
            return new Request();
        });

        // Registering the view component
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir('../apps/views/');

            return $view;
        });

        $di->set('url-short-jrpc', function () {
            return new JsonRpc('http://url-short');
        });

        $this->setDI($di);
    }

    public function main()
    {
        $this->registerServices();
        $this->registerAutoloaders();

        $response = $this->handle($_SERVER["REQUEST_URI"]);

        $response->send();
    }
}

try {
    $application = new Application();
    $application->main();
} catch (\Exception $e) {
    echo $e->getMessage();
}
