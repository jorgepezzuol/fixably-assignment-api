<?php

declare(strict_types=1);

namespace App\dto;

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

    public function __construct(int $statusCode, Note $note, string $message = '')
    {
        $this->statusCode = $statusCode;
        $this->note = $note;
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
