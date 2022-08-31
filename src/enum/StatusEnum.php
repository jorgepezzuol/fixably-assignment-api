<?php

declare(strict_types=1);

namespace App\Enum;

class StatusEnum
{
    public const STATUSES = [
        1 => 'Open',
        2 => 'Closed',
        3 => 'Assigned',
        4 => 'Unpaid'
    ];
}
