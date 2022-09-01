<?php

declare(strict_types=1);

namespace App\Helper;

use App\Enum\DeviceTypeEnum;
use App\Enum\StatusEnum;
use App\Model\Order;

class OrderHelper
{
    /**
     * @param array $orders
     *
     * @return array
     */
    public static function sortOrdersByStatus(array $orders): array
    {
        $results = $orders['results'] ?? [];
        $groupedOrders = [];
        $ordersAmount = count($results);

        foreach ($results as $order) {
            $status = StatusEnum::STATUSES[$order['status']] ?? 'Unknown';

            if (!isset($groupedOrders[$status])) {
                $groupedOrders[$status] = [];
                $groupedOrders[$status]['amount'] = 0;
                $groupedOrders[$status]['average'] = '';
            }

            $amount = $groupedOrders[$status]['amount'] += 1;
            $avg = ($amount / $ordersAmount) * 100;

            $groupedOrders[$status]['average'] = round($avg, 2) . '%';
            $groupedOrders[$status]['orders'][] = $order;
        }

        array_multisort(array_column($groupedOrders, 'amount'), SORT_DESC, $groupedOrders);

        return $groupedOrders;
    }

    /**
     * @param array $orders
     *
     * @return array
     */
    public static function filterByAssignedOrders(array $orders): array
    {
        $results = $orders['results'] ?? [];
        $assignedOrders = [];

        foreach ($results as $order) {
            $technician = $order['technician'] ?? null;

            if ($technician !== null) {
                $assignedOrders[] = $order;
            }
        }

        return $assignedOrders;
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    public static function isOrderValid(Order $order): bool
    {
        $errors = [];

        if (!is_string($order->getDeviceManufacturer()) || empty($order->getDeviceManufacturer())) {
            $errors[] = 'Device manufacturer is empty or not a string';
        }

        if (!is_string($order->getDeviceBrand()) || empty($order->getDeviceBrand())) {
            $errors[] = 'Device brand is empty or not a string';
        }

        if (
            !is_string($order->getDeviceType())
            || empty($order->getDeviceType())
            || !in_array($order->getDeviceType(), DeviceTypeEnum::DEVICE_TYPES)
        ) {
            $deviceTypes = implode(", ", DeviceTypeEnum::DEVICE_TYPES);
            $errors[] = "Device type is empty, not a string or not a valid type ({$deviceTypes})";
        }

        $order->setErrors($errors);

        return count($order->getErrors()) === 0;
    }
}
