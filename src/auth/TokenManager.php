<?php

declare(strict_types=1);

namespace App\Auth;

use App\Enum\FixablyEnum;
use DateTime;
use GuzzleHttp\Client;

class TokenManager
{
    private const FIXABLY_HEADER = 'X-Fixably-Token';
    private const MAX_FETCH_TOKEN_ATTEMPTS = 3;
    private const MAX_TOKEN_LIFESPAN_IN_MINUTES = 5; // ??

    /**
     * @var string
     */
    private string $token = '';

    /**
     * @var DateTime
     */
    private DateTime $tokenExpireTime;

    /**
     * @return array
     */
    public function createHeaders(): array
    {
        return [
            'headers' => [
                self::FIXABLY_HEADER => $this->fetchToken()
            ]
        ];
    }

    /**
     * @return bool
     */
    private function isTokenExpired(): bool
    {
        if (!$this->token || $this->tokenExpireTime >= new DateTime()) {
            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    private function setTokenExpireTime(): void
    {
        $maxTokenLifespan = self::MAX_TOKEN_LIFESPAN_IN_MINUTES;

        $this->tokenExpireTime = new DateTime();
        $this->tokenExpireTime->modify("+{$maxTokenLifespan}");
    }

    /**
     * @return string
     */
    private function fetchToken(): string
    {
        if (!$this->isTokenExpired()) {
            return $this->token;
        }

        $endpoint = sprintf('%s/token', FixablyEnum::API_URL);

        $requestBody = [
            'body' => [
                'Code' => $_ENV['FIXABLY_API_CODE']
            ]
        ];

        $attempt = 0;

        $guzzleClient = new Client();

        do {
            $response = $guzzleClient->post($endpoint, $requestBody);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $attempt += 1;
                sleep(1);
            }
        } while ($statusCode !== 200 && $attempt <= self::MAX_FETCH_TOKEN_ATTEMPTS);

        $this->setTokenExpireTime();

        return $this->token = $response->json()['token'] ?? 'EMPTY_TOKEN';
    }
}
