<?php

declare(strict_types=1);

namespace App\Test\Mock;

use App\Auth\TokenManager;

class TokenManagerMock
{
    /**
     * @return TokenManager
     */
    public static function getTokenManager(): TokenManager
    {
        return new class () extends TokenManager {
            private const EXPECTED_TOKEN = 'd16f6506f45cd0f1d8173bfd';

            /**
             * @return string
             */
            public function fetchToken(): string
            {
                return self::EXPECTED_TOKEN;
            }
        };
    }
}
