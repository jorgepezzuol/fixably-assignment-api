<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\OrderDto;
use App\Dto\OrderNoteDto;
use App\Dto\OrdersListDto;
use App\Enum\StatusEnum;
use App\Helper\OrderHelper;
use App\Model\Order;
use DateTime;
use Exception;

class OrderService extends AbstractBaseService
{
    /**
     * @return OrdersListDto
     */
    public function getOrdersByStatus(): OrdersListDto
    {
        $endpoint = sprintf('%s/orders', self::FIXABLY_API_URL);
        $page = 1;
        $orders = [];

        do {
            $url = sprintf('%s?page=%s', $endpoint, $page);
            $response = $this->guzzleClient->get($url, $this->createHeaders());
            $results = $response->json()['results'] ?? [];

            foreach ($results as $result) {
                $orders[] = $this->convertArrayToOrder($result);
            }

            $page += 1;
            $statusCode = $response->getStatusCode();
        } while ($statusCode === 200 && count($results) > 0);

        $sortedOrders = $this->sortOrdersByStatus($orders);

        return new OrdersListDto($statusCode, $sortedOrders);
    }

    /**
     * @param string $deviceBrand
     *
     * @return OrdersListDto
     */
    public function getAssignedOrdersByDevice(string $deviceBrand): OrdersListDto
    {
        $endpoint = sprintf('%s/search/devices', self::FIXABLY_API_URL);
        $requestBody = [
            'body' => [
                'Criteria' => sprintf('%s *', $deviceBrand)
            ]
        ];
        $page = 1;
        $orders = [];

        do {
            $url = sprintf('%s?page=%s', $endpoint, $page);
            $response = $this->guzzleClient->post($url, array_merge(
                $this->createHeaders(), $requestBody
            ));

            $results = $response->json()['results'] ?? [];

            foreach ($results as $result) {
                $orders[] = $this->convertArrayToOrder($result);
            }

            $page += 1;
            $statusCode = $response->getStatusCode();
        } while ($statusCode === 200 && count($results) > 0);

        $sortedOrders = $this->filterByAssignedOrders($orders);

        return new OrdersListDto($statusCode, $sortedOrders);
    }

    /**
     * @param Order $order
     *
     * @return OrderDto
     */
    public function createOrder(Order $order): OrderDto
    {
        if (!$order->isValid()) {
            return new OrderDto($order, 400, implode(', ', $order->getErrors()));
        }

        $endpoint = sprintf('%s/orders/create', self::FIXABLY_API_URL, 1);

        $requestBody = [
            'body' => [
                'DeviceManufacturer' => $order->getDeviceManufacturer(),
                'DeviceBrand' => $order->getDeviceBrand(),
                'DeviceType' => $order->getDeviceType(),
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->createHeaders(), $requestBody
        ));

        $message = 'Error while creating order';

        if ($response->getStatusCode() === 200 && isset($response->json()['id'])) {
            $order->setId($response->json()['id']);
            $message = sprintf('Order %s created', $order->getId());
        }

        return new OrderDto($order, $response->getStatusCode(), $message);
    }

    /**
     * @param Order[] $orders
     *
     * @return array
     */
    private function sortOrdersByStatus(array $orders): array
    {
        $groupedOrders = [];
        $ordersAmount = count($orders);

        foreach ($orders as $order) {
            $status = StatusEnum::STATUSES[$order->getStatus()];

            if (!isset($groupedOrders[$status])) {
                $groupedOrders[$status] = [];
                $groupedOrders[$status]['amount'] = 0;
                $groupedOrders[$status]['average'] = '';
            }

            $amount = $groupedOrders[$status]['amount'] += 1;
            $avg = ($amount / $ordersAmount) * 100;

            $groupedOrders[$status]['average'] = round($avg, 2) . '%';
            $groupedOrders[$status]['orders'][] = $order->toArray();
        }

        array_multisort(array_column($groupedOrders, 'amount'), SORT_DESC, $groupedOrders);

        return $groupedOrders;
    }

    /**
     * @param Order[] $orders
     *
     * @return array
     */
    private function filterByAssignedOrders(array $orders): array
    {
        $assignedOrders = [];

        foreach ($orders as $order) {
            $technician = $order->getTechnicianName() ?? null;

            if ($technician !== null) {
                $assignedOrders[] = $order->toArray();
            }
        }

        return $assignedOrders;
    }

    /**
     * @param array $result
     *
     * @return Order
     */
    private function convertArrayToOrder(array $result): Order
    {
        $order = new Order();
        $order->setId($result['id']);
        $order->setDeviceType($result['deviceType']);
        $order->setDeviceManufacturer($result['deviceManufacturer']);
        $order->setDeviceBrand($result['deviceBrand']);
        $order->setTechnicianName($result['technician']);
        $order->setStatus($result['status']);
        try {
            $order->setCreated(new DateTime($result['created']));
        } catch (Exception $e) {
            $order->setCreated(new DateTime());
        }

        return $order;
    }
}
