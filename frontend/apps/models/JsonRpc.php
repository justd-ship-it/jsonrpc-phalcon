<?php


namespace Frontend\Models;


class JsonRpc
{
    /**
     * @var string
     */
    protected $address;

    /**
     * JsonRpc constructor.
     * @param string $address
     */
    public function __construct(string $address)
    {
        $this->address = $address;
    }

    /**
     * @param $method
     * @param $params
     * @param bool $id
     * @return false|string
     */
    public function call($method, $params, $id = false)
    {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params
        ];

        if ($id !== false && (is_string($id) || is_int($id) || is_null($id))) {
            $request['id'] = $id;
        }

        $options = array(
            'http' => array(
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($request)
            )
        );

        $context = stream_context_create($options);
        $result = file_get_contents($this->address, false, $context);

        if ($result === FALSE) {
            return null;
        } else {
            return $result;
        }
    }
}
