<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\TokenManager;
use App\Enum\StatusEnum;
use App\Model\Note;
use App\Model\Order;
use App\Service\NoteService;
use App\Service\OrderService;
use DateTime;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CreateOrderAction extends AbstractBaseAction
{
    public function __invoke(ServerRequestInterface $httpRequest, ResponseInterface $httpResponse): ResponseInterface
    {
        try {
            $guzzleClient = new Client();
            $tokenManager = new TokenManager();

            $openStatus = StatusEnum::OPEN;

            $order = new Order();
            $order->setDeviceManufacturer('Apple');
            $order->setDeviceBrand('iPhone X');
            $order->setDeviceType('Phone');
            $order->setStatus(StatusEnum::STATUSES_ID[$openStatus]);
            $order->setCreated(new DateTime());

            $orderService = new OrderService($guzzleClient, $tokenManager);
            $createOrderResponse = $orderService->createOrder($order);
            $createdOrder = $createOrderResponse->getOrder();

            if ($createOrderResponse->getStatusCode() !== 200 || $createdOrder->getId() === 0) {
                return $this->writeJsonResponse($httpResponse, $this->getErrorResponse());
            }

            $note = new Note($createdOrder->getId(), 'Issue', 'test');
            $noteService = new NoteService($guzzleClient, $tokenManager);
            $createdNoteResponse = $noteService->createNote($note);
            $createdNote = $createdNoteResponse->getNote();

            if ($createdNoteResponse->getStatusCode() !== 200 || $createdNote->getId() === 0) {
                return $this->writeJsonResponse($httpResponse, $this->getErrorResponse());
            }

            $createdOrder->setNote($createdNote);

            $arrayResponse = [
                'status' => $createOrderResponse->getStatusCode(),
                'message' => $createOrderResponse->getMessage(),
                'data' => [
                    'orderId' => $createdOrder->getId(),
                    'noteId' => $createdNote->getId()
                ]
            ];

        } catch (Exception $exception) {
            return $this->writeJsonResponse($httpResponse, $this->getErrorResponse());
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
            'message' => 'Error while creating order',
            'data' => []
        ];
    }
}
