<?php


namespace ShortUrl\JsonRpc\Exception;


class InvalidParamsException extends JsonRpcException
{
    protected $code = -32602;
    protected $message = 'Invalid params';
}
