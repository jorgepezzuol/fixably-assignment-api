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
}
