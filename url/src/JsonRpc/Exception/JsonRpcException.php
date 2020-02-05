<?php

namespace ShortUrl\JsonRpc\Exception;

class JsonRpcException extends \Exception
{
    protected $code = -32603;
    protected $message = 'Internal error';
    protected $data = null;

    /**
     * JsonRpcException constructor.
     * @param string $message
     */
    public function __construct(string $message = null)
    {
        parent::__construct();
        $this->setData($message);
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(?string $data): void
    {
        $this->data = $data;
    }


}
