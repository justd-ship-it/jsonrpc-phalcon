<?php

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/src');

use ShortUrl\JsonRpc\Exception;
use Phalcon\Mvc\Application;

try {

    /**
     * Read the configuration
     */
    $config = include __DIR__ . "/../config/config.php";

    /**
     * Read auto-loader
     */
    include __DIR__ . "/../config/loader.php";

    /**
     * Read services
     */
    include __DIR__ . "/../config/services.php";

    /**
     * Handle the request
     */

    /** @var \Phalcon\Http\Request $request */
    $request = $di->get('request');

    $requestBody = json_decode($request->getRawBody());

    $application = new Application();
    $application->setDI($di);

    $handleJsonRpc = function ($routeItem) use ($application) {
        $response = [
            'json_rpc' => '2.0'
        ];

        try {
            if (!is_object($routeItem)) {
                throw new Exception\InvalidRequestException();
            }

            $application->router->setJsonRouting($routeItem);
            $application->handle('');
            $response['result'] = $application->dispatcher->getReturnedValue();
        } catch (\Phalcon\Mvc\Dispatcher\Exception  $exception) {
            // не знаю все ли коды являются ошибками отсутствия метода
            if (in_array($exception->getCode(), [2, 5])) {
                throw new Exception\MethodNotFoundException();
            }
            throw $exception;
        } catch (Exception\JsonRpcException $rpcException) {
            $response['error'] = [
                'code' => $rpcException->getCode(),
                'message' => $rpcException->getMessage()
            ];

            if ($data = $rpcException->getData()) {
                $response['error']['data'] = $data;
            }

            if ($rpcException instanceof Exception\ParseErrorException || $rpcException instanceof Exception\InvalidRequestException) {
                $response['id'] = null;
            }

        } catch (\Exception $e) {
            $response['error'] = [
                'code' => -32603,
                'message' => 'Internal error',
                'data' => $e->getMessage()
            ];
        }

        if (isset($response['id'])) {
            return $response;
        } elseif ($routeItem->id) {
            $response['id'] = $routeItem->id;
            return $response;
        } else {
            return null;
        }
    };

    if (is_array($requestBody)) {
        $responseBody = [];

        foreach ($requestBody as $requestItem) {
            $handle = $handleJsonRpc($requestItem);

            if (!is_null($handle)) {
                $responseBody[] = $handle;
            }
        }
    } else {
        $handle = $handleJsonRpc($requestBody);
        if (!is_null($handle)) {
            $responseBody = json_encode($handle);
        }
    }

    if (!empty($responseBody)) {
        $response = $application->response;

        $response->setContentType('application/json', 'UTF-8');

        if (is_string($responseBody)) {
            $response->setContent($responseBody);
        } else {
            $response->setContent(json_encode($responseBody));
        }

        $response->send();
    }

} catch (\Exception $e) {
    // Считаем что Эксепшены которые вываливаются по пути являются ошибками уровня реализации приложения
    // По идее это совсем не "стандартная" ситуация и сюда мы никогда не должны попасть.
    // - The remainder of the space is available for application defined errors.
    echo json_encode([
        'jsonrpc' => '2.0',
        'error' => [
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'data' => get_class($e)
        ],
        'id' => null
    ]);
}
