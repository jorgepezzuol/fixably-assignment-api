<?php

declare(strict_types=1);

namespace App\Enum;

class DeviceTypeEnum
{
    public const LAPTOP = 'Laptop';
    public const PHONE = 'Phone';
    public const TABLET = 'Tablet';

    public const TYPES = [
        self::LAPTOP,
        self::PHONE,
        self::TABLET
    ];
}

