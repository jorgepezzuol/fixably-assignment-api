<?php

declare(strict_types=1);

namespace App\Dto;

use App\Model\Note;

class NoteDto
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var Note
     */
    private Note $note;

    /**
     * @var string
     */
    private string $message;

    public function __construct(Note $note, int $statusCode, string $message = '')
    {
        $this->note = $note;
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    /**
     * @return Note
     */
    public function getNote(): Note
    {
        return $this->note;
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
