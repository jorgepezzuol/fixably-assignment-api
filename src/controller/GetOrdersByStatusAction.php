<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\TokenManager;
use App\Service\OrderService;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetOrdersByStatusAction extends AbstractBaseAction
{
    public function __invoke(ServerRequestInterface $httpRequest, ResponseInterface $httpResponse): ResponseInterface
    {
        try {
            $orderService = new OrderService(new Client(), new TokenManager());
            $response = $orderService->getOrdersByStatus();

            $arrayResponse = [
                'status' => $response->getStatusCode(),
                'message' => 'OK',
                'data' => [
                    'ordersByStatus' => $response->getOrders()
                ]
            ];

        } catch (Exception $exception) {
            $arrayResponse = $this->getErrorResponse();
        }

        return $this->writeJsonResponse($httpResponse, $arrayResponse);
    }

    /**
     * @param ResponseInterface $httpResponse
     * @param array             $response
     *
     * @return ResponseInterface
     */
    protected function writeJsonResponse(ResponseInterface $httpResponse, array $response): ResponseInterface
    {
        $json = json_encode($response);

        $httpResponse->getBody()->write($json);

        return $httpResponse->withStatus($response['status'])->withHeader('Content-type', 'application/json');
    }

    /**
     * @return array
     */
    protected function getErrorResponse(): array
    {
        return [
            'status' => 500,
            'message' => 'Error while fetching orders',
            'data' => []
        ];
    }
}
