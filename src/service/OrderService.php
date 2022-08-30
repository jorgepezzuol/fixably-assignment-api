<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\GetOrdersByStatusDto;
use App\helper\OrderHelper;

class OrderService extends AbstractFixablyApiService
{

    /**
     * @param int $page
     *
     * @return GetOrdersByStatusDto
     */
    public function getOrdersByStatus(int $page = 1): GetOrdersByStatusDto
    {
        $endpoint = sprintf('%s/orders?page=%s', self::FIXABLY_API_URL, $page);

        $response = $this->client->get($endpoint, $this->createHeaderWithToken());

        $formattedOrders = OrderHelper::formatOrdersByStatus($response->json());

        return new GetOrdersByStatusDto($response->getStatusCode(), $page, $formattedOrders);
    }

    public function getAssignedOrdersByDevice(string $deviceBrand = 'iPhone', int $page = 1)
    {

    }
}
