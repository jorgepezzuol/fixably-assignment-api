<?php

declare(strict_types=1);

namespace App\Dto;

class GetOrdersByStatusDto
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var int
     */
    private int $page;

    /**
     * @var array
     */
    private array $orders;

    public function __construct(int $statusCode, int $page, array $orders = [])
    {
        $this->statusCode = $statusCode;
        $this->page = $page;
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
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return array
     */
    public function getOrders(): array
    {
        return $this->orders;
    }
}
