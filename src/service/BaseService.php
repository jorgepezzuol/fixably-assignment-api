<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\FetchTokenDto;
use App\enum\FixablyEnum;
use GuzzleHttp\Client;

abstract class BaseService
{
    protected const FIXABLY_HEADER = 'X-Fixably-Token';

    private const MAX_FETCH_TOKEN_ATTEMPTS = 3;

    /**
     * @var Client
     */
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    protected function createHeaderWithToken(): array
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
     * @return FetchTokenDto
     */
    private function fetchToken(): FetchTokenDto
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

        $token = $response->json()['token'] ?? 'EMPTY_TOKEN';

        return new FetchTokenDto($statusCode, $token);
    }
}
