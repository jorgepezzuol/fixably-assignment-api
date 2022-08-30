<?php

namespace App\helper;

class OrderHelper
{

    public static function formatOrdersByStatus(array $orders = []): array
    {
        $results = $orders['results'] ?? [];
        $groupedOrders = [];

        foreach ($results as $order) {
            $status = $order['status'];

            if (!array_key_exists($status, $groupedOrders)) {
                $groupedOrders[$status] = [];
            }

            $groupedOrders[$status][] = $order;
        }

//        echo '<pre>';
//        print_r($groupedOrders);
//        exit;

        uksort($groupedOrders, function ($i, $j) use ($results) {
            return $results[$i]['status'] > $results[$j]['status'];
        });

        return $groupedOrders;
    }
}
