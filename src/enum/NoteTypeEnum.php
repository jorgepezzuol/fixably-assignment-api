<?php

declare(strict_types=1);

namespace App\Enum;

class NoteTypeEnum
{
    public const ISSUE = 'Issue';
    public const DIAGNOSIS = 'Diagnosis';
    public const RESOLUTION = 'Resolution';

    public const TYPES = [
        self::ISSUE,
        self::DIAGNOSIS,
        self::RESOLUTION
    ];
}
