<?php

declare(strict_types=1);

namespace App\Model;

use App\Enum\NoteTypeEnum;

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
     * @param int    $orderId
     * @param string $type
     * @param string $description
     */
    public function __construct(int $orderId, string $type, string $description)
    {
        $this->orderId = $orderId;
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
     * @return bool
     */
    public function isValid(): bool
    {
        $errors = [];

        if ($this->getOrderId() <= 0) {
            $errors[] = 'Order id is not valid';
        }

        if (!is_string($this->getDescription()) || empty($this->getDescription())) {
            $errors[] = 'Note description is empty or not a string';
        }

        if (
            !is_string($this->getType())
            || empty($this->getType())
            || !in_array($this->getType(), NoteTypeEnum::TYPES)
        ) {
            $noteTypes = implode(", ", NoteTypeEnum::TYPES);
            $errors[] = "Note type is empty, not a string or not a valid type ({$noteTypes})";
        }

        $this->setErrors($errors);

        return count($this->getErrors()) === 0;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'orderId' => $this->getOrderId(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
        ];
    }
}
