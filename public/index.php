<?php

declare(strict_types=1);

use App\Dto\OrderNoteDto;
use App\Model\Note;
use App\Model\Order;
use App\Service\OrderService;
use App\Service\TokenService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = AppFactory::create();

$app->get('/orders', function (
    ServerRequestInterface $httpRequest,
    ResponseInterface $httpResponse
): ResponseInterface {
    try {
        $orderService = new OrderService(new Client());
        $response = $orderService->getOrdersByStatus();

        $httpResponse->getBody()->write(json_encode(
            [
                'status' => $response->getStatusCode(),
                'data' => [
                    'page' => $response->getPage(),
                    'ordersByStatus' => $response->getOrders()
                ]
            ]
        ));

    } catch (Exception $exception) {

    }

    return $httpResponse->withHeader('Content-type', 'application/json');;
});

$app->get('/orders/assigned', function (
    ServerRequestInterface $httpRequest,
    ResponseInterface $httpResponse
): ResponseInterface {
    try {
        $orderService = new OrderService(new Client());
        $response = $orderService->getAssignedOrdersByDevice();

        $httpResponse->getBody()->write(json_encode(
            [
                'status' => $response->getStatusCode(),
                'data' => [
                    'page' => $response->getPage(),
                    'assignedOrders' => $response->getOrders()
                ]
            ]
        ));

    } catch (Exception $exception) {

    }

    return $httpResponse->withHeader('Content-type', 'application/json');;
});

$app->post('/orders/create', function (
    ServerRequestInterface $httpRequest,
    ResponseInterface $httpResponse
): ResponseInterface {
    try {
        $postParams = $httpRequest->getParsedBody();

        $order = new Order();
        $order->setDeviceManufacturer($postParams['DeviceManufacturer']);
        $order->setDeviceBrand($postParams['DeviceBrand']);
        $order->setDeviceType($postParams['DeviceType']);

        $note = new Note($postParams['Type'], $postParams['NoteDescription']);

        $orderService = new OrderService(new Client());
        $response = $orderService->createOrderWithNote($order, $note);

        $httpResponse->getBody()->write(json_encode(
            [
                'status' => $response->getStatusCode(),
                'data' => [
                    'message' => $response->getMessage(),
                    'orderId' => $response->getOrder()->getId(),
                    'noteId' => $response->getNoteId(),
                ]
            ]
        ));

    } catch (Exception $exception) {

    }

    return $httpResponse->withHeader('Content-type', 'application/json');;
});

$app->run();
