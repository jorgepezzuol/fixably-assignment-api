<?php

declare(strict_types=1);

namespace App\Helper;

use App\Enum\StatusEnum;

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
}
