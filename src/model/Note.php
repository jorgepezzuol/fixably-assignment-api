<?php

declare(strict_types=1);

namespace App\Model;

class Note extends AbstractBaseModel
{
    /**
     * @var int
     */
    private int $orderId;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $description;


    /**
     * @param string $type
     * @param string $description
     */
    public function __construct(string $type, string $description)
    {
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }
}
