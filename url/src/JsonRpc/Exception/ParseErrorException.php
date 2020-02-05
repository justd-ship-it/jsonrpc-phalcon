<?php


namespace ShortUrl\JsonRpc\Exception;


class ParseErrorException extends JsonRpcException
{
    protected $code = -32700;
    protected $message = 'Parse error';
}
