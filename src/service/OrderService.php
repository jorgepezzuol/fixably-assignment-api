<?php

declare(strict_types=1);

namespace App\Service;

use App\Auth\TokenManager;
use App\Dto\NoteDto;
use App\Dto\OrderDto;
use App\Dto\OrderNoteDto;
use App\Dto\OrdersListDto;
use App\Enum\FixablyEnum;
use App\Helper\NoteHelper;
use App\Helper\OrderHelper;
use App\Model\Order;
use App\Model\Note;
use GuzzleHttp\Client;

class OrderService
{
    /**
     * @var Client
     */
    private Client $guzzleClient;

    /**
     * @var TokenManager
     */
    private TokenManager $tokenManager;

    /**
     * @param Client       $guzzleClient
     * @param TokenManager $tokenManager
     */
    public function __construct(Client $guzzleClient, TokenManager $tokenManager)
    {
        $this->guzzleClient = $guzzleClient;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param int $page
     *
     * @return OrdersListDto
     */
    public function getOrdersByStatus(int $page = 1): OrdersListDto
    {
        $endpoint = sprintf('%s/orders?page=%s', FixablyEnum::API_URL, 33);

        $response = $this->guzzleClient->get($endpoint, $this->tokenManager->createHeaders());

        $sortedOrders = OrderHelper::sortOrdersByStatus($response->json());

        return new OrdersListDto($response->getStatusCode(), $page, $sortedOrders);
    }

    /**
     * @param string $device
     * @param int    $page
     *
     * @return OrdersListDto
     */
    public function getAssignedOrdersByDevice(string $device = 'iPhone', int $page = 1): OrdersListDto
    {
        $endpoint = sprintf('%s/search/devices?page=%s', FixablyEnum::API_URL, 1);

        $requestBody = [
            'body' => [
                'Criteria' => $device
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->tokenManager->createHeaders(), $requestBody
        ));

        $assingedOrders = OrderHelper::filterByAssignedOrders($response->json());

        return new OrdersListDto($response->getStatusCode(), $page, $assingedOrders);
    }

    /**
     * @param Order $order
     *
     * @return OrderDto
     */
    public function createOrder(Order $order): OrderDto
    {
        if (!OrderHelper::isOrderValid($order)) {
            return new OrderDto($order, 400, implode(', ', $order->getErrors()));
        }

        $endpoint = sprintf('%s/orders/create', FixablyEnum::API_URL, 1);

        $requestBody = [
            'body' => [
                'DeviceManufacturer' => $order->getDeviceManufacturer(),
                'DeviceBrand' => $order->getDeviceBrand(),
                'DeviceType' => $order->getDeviceType(),
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->tokenManager->createHeaders(), $requestBody
        ));

        $message = 'Error while creating order';

        if ($response->getStatusCode() === 200 && isset($response->json()['id'])) {
            $order->setId($response->json()['id']);
            $message = sprintf('Order %s created', $order->getId());
        }

        return new OrderDto($order, $response->getStatusCode(), $message);
    }

    /**
     * @param Note $note
     *
     * @return NoteDto
     * @throws Exception
     */
    public function createNote(Note $note): NoteDto
    {
        if (!NoteHelper::isNoteValid($note)) {
            return new NoteDto($note, 400, implode(', ', $note->getErrors()));
        }

        $endpoint = sprintf('%s/orders/%s/notes/create', FixablyEnum::API_URL, $note->getOrderId());

        $requestBody = [
            'body' => [
                'Type' => $note->getType(),
                'Description' => $note->getDescription(),
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->tokenManager->createHeaders(), $requestBody
        ));

        $message = 'Error while creating order';

        if ($response->getStatusCode() === 200 && isset($response->json()['id'])) {
            $note->setId($response->json()['id']);
            $message = sprintf('Note %s created', $note->getId());
        }

        return new NoteDto($note, $response->getStatusCode(), $message);
    }
}
