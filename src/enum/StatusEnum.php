<?php

declare(strict_types=1);

namespace App\Enum;

class StatusEnum
{
    public const OPEN = 'Open';
    public const CLOSED = 'Closed';
    public const ASSIGNED = 'Assigned';
    public const UNPAID = 'Unpaid';

    public const STATUSES = [
        1 => self::OPEN,
        2 => self::CLOSED,
        3 => self::ASSIGNED,
        4 => self::UNPAID
    ];

    public const STATUSES_ID = [
        self::OPEN => 1,
        self::CLOSED => 2,
        self::ASSIGNED => 3,
        self::UNPAID => 4,
    ];
}
