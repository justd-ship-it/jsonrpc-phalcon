<?php


namespace ShortUrl\JsonRpc\Exception;


class MethodNotFoundException extends JsonRpcException
{
    protected $code = -32601;
    protected $message = 'Method not found';
}
