<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\TokenManager;
use App\Model\Note;
use App\Model\Order;
use App\Service\NoteService;
use App\Service\OrderService;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateOrderAction
{
    public function __invoke(ServerRequestInterface $httpRequest, ResponseInterface $httpResponse): ResponseInterface
    {
        try {
            $postParams = $httpRequest->getParsedBody();

            $guzzleClient = new Client();
            $tokenManager = new TokenManager();

            $order = new Order();
//            $order->setDeviceManufacturer($postParams['DeviceManufacturer']);
//            $order->setDeviceBrand($postParams['DeviceBrand']);
//            $order->setDeviceType($postParams['DeviceType']);
            $order->setDeviceManufacturer('Apple');
            $order->setDeviceBrand('iPhone X');
            $order->setDeviceType('Phone');

            $orderService = new OrderService($guzzleClient, $tokenManager);
            $createOrderResponse = $orderService->createOrder($order);
            $createdOrder = $createOrderResponse->getOrder();

            if ($createOrderResponse->getStatusCode() !== 200 || $createdOrder->getId() === 0) {
                return $this->writeJsonResponse(
                    $httpResponse, $createOrderResponse->getStatusCode(), $createOrderResponse->getMessage()
                );
            }

//            $note = new Note($createdOrder->getId(), $postParams['NoteType'], $postParams['NoteDescription']);
            $note = new Note($createdOrder->getId(), 'Issue', 'test');
            $noteService = new NoteService($guzzleClient, $tokenManager);
            $createdNoteResponse = $noteService->createNote($note);
            $createdNote = $createdNoteResponse->getNote();

            if ($createdNoteResponse->getStatusCode() !== 200 || $createdNote->getId() === 0) {
                return $this->writeJsonResponse(
                    $httpResponse, $createdNoteResponse->getStatusCode(), $createdNoteResponse->getMessage()
                );
            }

            $createdOrder->setNote($createdNote);

            $httpResponse = $this->writeJsonResponse(
                $httpResponse,
                $createOrderResponse->getStatusCode(),
                $createOrderResponse->getMessage(),
                $createdOrder->getId(),
                $createdNote->getId()
            );

        } catch (Exception $exception) {
            return $this->writeJsonResponse($httpResponse, 400, 'Unknown error');
        }

        return $httpResponse;
    }


    /**
     * @param ResponseInterface $httpResponse
     * @param int               $statusCode
     * @param string            $message
     * @param int|null          $orderId
     * @param int|null          $noteId
     *
     * @return ResponseInterface
     */
    private function writeJsonResponse(
        ResponseInterface $httpResponse,
        int $statusCode,
        string $message,
        ?int $orderId = 0,
        ?int $noteId = 0
    ): ResponseInterface {
        $json = json_encode(
            [
                'status' => $statusCode,
                'message' => $message,
                'data' => [
                    'orderId' => $orderId,
                    'noteId' => $noteId
                ]
            ]
        );

        $httpResponse->getBody()->write($json);

        return $httpResponse->withStatus($statusCode)->withHeader('Content-type', 'application/json');
    }
}
