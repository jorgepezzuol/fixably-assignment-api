<?php

declare(strict_types=1);

namespace App\Service;

use App\dto\NoteDto;
use App\Dto\OrderDto;
use App\Dto\OrderNoteDto;
use App\Dto\OrdersListDto;
use App\enum\FixablyEnum;
use App\Helper\OrderHelper;
use App\Model\Order;
use App\Model\Note;
use Exception;
use GuzzleHttp\Client;

class OrderService extends BaseService
{
    /**
     * @param int $page
     *
     * @return OrdersListDto
     */
    public function getOrdersByStatus(int $page = 1): OrdersListDto
    {
        $endpoint = sprintf('%s/orders?page=%s', FixablyEnum::API_URL, 33);

        $response = $this->client->get($endpoint, $this->createHeaderWithToken());

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

        $response = $this->client->post($endpoint, array_merge(
            $this->createHeaderWithToken(), $requestBody
        ));

        $assingedOrders = OrderHelper::filterByAssignedOrders($response->json());

        return new OrdersListDto($response->getStatusCode(), $page, $assingedOrders);
    }

    /**
     * @param Order $orderToBeCreated
     * @param Note  $noteToBeCreated
     *
     * @return OrderDto
     * @throws Exception
     */
    public function createOrderWithNote(Order $orderToBeCreated, Note $noteToBeCreated): OrderDto
    {
        $createOrderResponse = $this->createOrder($orderToBeCreated);

        if ($createOrderResponse->getStatusCode() !== 200 || $createOrderResponse->getOrder()->getId() === 0) {
            return new OrderDto($orderToBeCreated, $statusCode, 'Error while creating order');
        }

        $orderCreated = $createOrderResponse->getOrder();
        $noteToBeCreated->setOrderId($orderCreated->getId());
        $createNoteResponse = $this->createNote($noteToBeCreated);

        if ($createNoteResponse->getStatusCode() !== 200 || $createNoteResponse->getNote()->getId() === 0) {
            $errorMessage = sprintf('Error while creating note for order: %s', $orderCreated->getId());
            return new OrderDto($orderCreated, $createNoteResponse->getStatusCode(), $errorMessage);
        }

        $orderCreated->setNote($createNoteResponse->getNote());

        return new OrderDto($order, 200, 'Order and note created');
    }

    /**
     * @param Order $order
     *
     * @return OrderDto
     * @throws Exception
     */
    public function createOrder(Order $order): OrderDto
    {
        $endpoint = sprintf('%s/orders/create', FixablyEnum::API_URL, 1);

        $requestBody = [
            'body' => [
                'DeviceManufacturer' => $order->getDeviceManufacturer(),
                'DeviceBrand' => $order->getDeviceBrand(),
                'DeviceType' => $order->getDeviceType(),
            ]
        ];

        $response = $this->client->post($endpoint, array_merge(
            $this->createHeaderWithToken(), $requestBody
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
        $endpoint = sprintf('%s/orders/%s/notes/create', FixablyEnum::API_URL, $note->getOrderId());

        $requestBody = [
            'body' => [
                'Type' => $note->getType(),
                'Description' => $note->getDescription(),
            ]
        ];

        $response = $this->client->post($endpoint, array_merge(
            $this->createHeaderWithToken(), $requestBody
        ));

        $message = 'Error while creating order';

        if ($response->getStatusCode() === 200 && isset($response->json()['id'])) {
            $note->setId($response->json()['id']);
            $message = sprintf('Note %s created', $note->getId());
        }

        return new NoteDto($response->getStatusCode(), $note, $message);
    }
}
