<?php


namespace ShortUrl\JsonRpc;

use Phalcon\Mvc\Router as PhalconRouter;
use ShortUrl\JsonRpc\Exception;

class Router extends PhalconRouter
{

    /**
     * @param object $routeData
     * @throws Exception\InvalidRequestException
     * @throws Exception\ParseErrorException
     */
    public function setJsonRouting(object $routeData): void
    {
        if (is_null($routeData)) {
            throw new Exception\ParseErrorException();
        }

        if (!isset($routeData->jsonrpc) || !isset($routeData->method)) {
            throw new Exception\InvalidRequestException();
        }

        $method = explode('.', $routeData->method);

        $this->controller = $method[0];
        $this->action = $method[1] ?: 'index';
        $this->params = get_object_vars($routeData->params);
        $this->params['__id'] = $routeData->id ?: null;
    }

    public function handle(string $uri): void
    {
        // nothing
    }

}
