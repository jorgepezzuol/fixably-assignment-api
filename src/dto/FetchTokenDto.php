<?php

declare(strict_types=1);

namespace App\Dto;

class FetchTokenDto
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var string
     */
    private string $token;

    public function __construct(int $statusCode, string $token)
    {
        $this->statusCode = $statusCode;
        $this->token = $token;
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
    public function getToken(): string
    {
        return $this->token;
    }
}
