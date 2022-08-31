<?php

declare(strict_types=1);

namespace App\Auth;

use App\Dto\FetchTokenDto;
use App\Enum\FixablyEnum;

class TokenManager
{
    private const FIXABLY_HEADER = 'X-Fixably-Token';
    private const MAX_FETCH_TOKEN_ATTEMPTS = 3;

    /**
     * @var Token
     */
    private Token $token;

    /**
     * @return array
     */
    public function createHeaderWithToken(): array
    {
        $response = $this->fetchToken();
        $token = $response->getToken();

        return [
            'headers' => [
                self::FIXABLY_HEADER => $token
            ]
        ];
    }

    /**
     * @return Token
     */
    private function fetchToken(): Token
    {

        $endpoint = sprintf('%s/token', FixablyEnum::API_URL);
        $attempt = 0;
        $requestBody = [
            'body' => [
                'Code' => $_ENV['FIXABLY_API_CODE']
            ]
        ];

        do {
            $response = $this->client->post($endpoint, $requestBody);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $attempt += 1;
                sleep(1);
            }
        } while ($statusCode !== 200 && $attempt <= self::MAX_FETCH_TOKEN_ATTEMPTS);

        $tokenString = $response->json()['token'] ?? 'EMPTY_TOKEN';
    }
}
