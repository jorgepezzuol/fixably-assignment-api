<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\TokenManager;
use App\Service\OrderService;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetAssignedOrdersByDeviceAction
{
    public function __invoke(ServerRequestInterface $httpRequest, ResponseInterface $httpResponse): ResponseInterface {
        try {
            $orderService = new OrderService(new Client(), new TokenManager());
            $response = $orderService->getAssignedOrdersByDevice('iPhone');

            $httpResponse->getBody()->write(json_encode(
                [
                    'status' => $response->getStatusCode(),
                    'data' => [
                        'assignedOrders' => $response->getOrders()
                    ]
                ]
            ));

        } catch (Exception $exception) {
            var_dump($exception->getMessage());
        }

        return $httpResponse->withHeader('Content-type', 'application/json');
    }
}
