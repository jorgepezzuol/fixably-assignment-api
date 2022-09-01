<?php

declare(strict_types=1);

use App\Auth\TokenManager;
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
        $orderService = new OrderService(new Client(), new TokenManager());
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
        var_dump($exception->getMessage());
    }

    return $httpResponse->withHeader('Content-type', 'application/json');;
});

$app->get('/orders/assigned', function (
    ServerRequestInterface $httpRequest,
    ResponseInterface $httpResponse
): ResponseInterface {
    try {
        $orderService = new OrderService(new Client(), new TokenManager());
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
        var_dump($exception->getMessage());
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

        $orderService = new OrderService(new Client(), new TokenManager());
        $createOrderResponse = $orderService->createOrder($order);
        $createdOrder = $createOrderResponse->getOrder();

        if ($createOrderResponse->getStatusCode() !== 200 && $createdOrder->getId() === 0) {
            $httpResponse->getBody()->write(json_encode(
                [
                    'status' => $createOrderResponse->getStatusCode(),
                    'data' => [
                        'message' => $createOrderResponse->getMessage(),
                    ]
                ]
            ));
            return $httpResponse->withStatus($createOrderResponse->getStatusCode())->withHeader('Content-type', 'application/json');;
        }

        $note = new Note($createdOrder->getId(), $postParams['NoteType'], $postParams['NoteDescription']);
        $createdNoteResponse = $orderService->createNote($note);
        $createdNote = $createdNoteResponse->getNote();

        if ($createdNoteResponse->getStatusCode() !== 200 && $createdNote->getId() === 0) {
            $httpResponse->getBody()->write(json_encode(
                [
                    'status' => $createdNoteResponse->getStatusCode(),
                    'data' => [
                        'message' => $createdNoteResponse->getMessage(),
                    ]
                ]
            ));
            return $httpResponse->withStatus($createdNoteResponse->getStatusCode())->withHeader('Content-type', 'application/json');;
        }

        $createdOrder->setNote($createdNote);

        $httpResponse->getBody()->write(json_encode(
            [
                'status' => $createdNoteResponse->getStatusCode(),
                'data' => [
                    'message' => $createOrderResponse->getMessage(),
                    'orderId' => $createdOrder->getId(),
                    'noteId' => $createdOrder->getNote()->getId()
                ]
            ]
        ));

    } catch (Exception $exception) {
        var_dump($exception->getMessage() . ' - ' . $exception->getTrace());
    }

    return $httpResponse->withStatus(200)->withHeader('Content-type', 'application/json');;
});

$app->run();
