<?php


namespace ShortUrl\JsonRpc\Exception;


class RouteException extends JsonRpcException
{
    protected $code = -32601;
    protected $message = 'Method not found';
}
