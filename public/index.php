<?php

declare(strict_types=1);

use App\Service\OrderService;
use App\service\TokenService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app->get('/orders', function (
    ServerRequestInterface $httpRequest,
    ResponseInterface $httpResponse
): ResponseInterface {
    try {
        $service = new OrderService(new Client());
        $response = $service->getOrdersByStatus();

        $httpResponse->getBody()->write(json_encode(
            [
                'status' => $response->getStatusCode(),
                'data' => [
                    'page' => $response->getPage(),
                    'orders' => $response->getOrders()
                ]
            ]
        ));

    } catch (Exception $exception) {

    }

    return $httpResponse->withHeader('Content-type', 'application/json');;
});

$app->run();
