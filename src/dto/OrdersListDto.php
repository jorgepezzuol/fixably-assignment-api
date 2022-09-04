<?php

declare(strict_types=1);

namespace App\Dto;

use App\Model\Order;

class OrdersListDto
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var Order[]
     */
    private array $orders;

    public function __construct(int $statusCode, array $orders)
    {
        $this->statusCode = $statusCode;
        $this->orders = $orders;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getOrders(): array
    {
        return $this->orders;
    }
}
