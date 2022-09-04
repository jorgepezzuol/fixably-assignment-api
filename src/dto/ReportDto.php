<?php

declare(strict_types=1);

namespace App\Dto;

class ReportDto
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var array
     */
    private array $report;

    /**
     * @var string
     */
    private string $message;
    
    /**
     * @param int    $statusCode
     * @param array  $report
     * @param string $message
     */
    public function __construct(int $statusCode, array $report, string $message)
    {
        $this->statusCode = $statusCode;
        $this->report = $report;
        $this->message = $message;
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
    public function getReport(): array
    {
        return $this->report;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
