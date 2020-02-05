<?php


namespace ShortUrl\JsonRpc\Exception;


class InvalidRequestException extends JsonRpcException
{
    protected $code = -32600;
    protected $message = 'Invalid Request';
}
