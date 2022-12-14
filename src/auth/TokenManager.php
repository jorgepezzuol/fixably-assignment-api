<?php

declare(strict_types=1);

namespace App\Auth;

use DateTime;
use GuzzleHttp\Client;

class TokenManager
{
    private const TOKEN_ENDPOINT = 'https://careers-api.fixably.com/token';
    private const MAX_TOKEN_LIFESPAN_IN_MINUTES = 1; // ??

    /**
     * @var string
     */
    private string $token = '';

    /**
     * @var DateTime
     */
    private DateTime $tokenExpireTime;

    /**
     * @return string
     */
    public function fetchToken(): string
    {
        if (!$this->isTokenExpired()) {
            return $this->token;
        }

        $requestBody = [
            'body' => [
                'Code' => $_ENV['FIXABLY_API_CODE']
            ]
        ];

        $guzzleClient = new Client();
        $response = $guzzleClient->post(self::TOKEN_ENDPOINT, $requestBody);
        $this->setTokenExpireTime();

        return $this->token = $response->json()['token'] ?? 'EMPTY_TOKEN';
    }

    /**
     * @return bool
     */
    private function isTokenExpired(): bool
    {
        return empty($this->token) || new DateTime() >= $this->tokenExpireTime;
    }

    /**
     * @return void
     */
    private function setTokenExpireTime(): void
    {
        $maxTokenLifespan = self::MAX_TOKEN_LIFESPAN_IN_MINUTES;

        $this->tokenExpireTime = new DateTime();
        $this->tokenExpireTime->modify("+{$maxTokenLifespan} minutes");
    }
}
