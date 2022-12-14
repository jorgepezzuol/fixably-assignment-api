<?php

declare(strict_types=1);

namespace App\Model;

abstract class AbstractBaseModel
{
    /**
     * @var int
     */
    private int $id = 0;

    /**
     * @var array
     */
    private array $errors;

    /**
     * @return bool
     */
    public abstract function isValid(): bool;

    /**
     * @return array
     */
    public abstract function toArray(): array;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}
