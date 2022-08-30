<?php

namespace App\service;

use App\Dto\FetchTokenDto;
use GuzzleHttp\Client;

abstract class AbstractFixablyApiService
{
    protected const FIXABLY_API_URL = 'https://careers-api.fixably.com';
    protected const FIXABLY_HEADER = 'X-Fixably-Token';

    /**
     * @var Client
     */
    protected Client $client;

    private const MAX_FETCH_TOKEN_ATTEMPTS = 3;

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
        $endpoint = sprintf('%s/token', self::FIXABLY_API_URL);
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
