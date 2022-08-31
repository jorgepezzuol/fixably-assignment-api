<?php

declare(strict_types=1);

namespace App\Dto;

use App\Model\Order;

class OrderDto
{
    /**
     * @var Order
     */
    private Order $order;

    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var string
     */
    private string $message;

    /**
     * @var int
     */
    private int $noteId = 0;

    public function __construct(Order $order, int $statusCode, string $message = '')
    {
        $this->order = $order;
        $this->statusCode = $statusCode;
        $this->message = $message;

        $note = $order->getNote();

        if ($note) {
            $this->noteId = $note->getId();
        }
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }


    public function getNoteId(): int
    {
        return $this->noteId;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }


    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
