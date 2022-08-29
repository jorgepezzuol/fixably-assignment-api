<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use App\Dto\TokenResponseDto;


// TODO: If no JSON can be returned for any reason, your code must fallback to the HTTP response code.
class FixablyApiService
{
    private const FIXABLY_API_URL = 'https://careers-api.fixably.com';
    private const FIXABLY_API_CODE = 65275210;
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
     * @return TokenResponseDto
     */
    public function fetchToken(): TokenResponseDto
    {
        $tokenEndpoint = self::FIXABLY_API_URL . '/token';
        $attempts = 0;

        do {
            $response = $this->client->post($tokenEndpoint, [
                'body' => [
                    'Code' => self::FIXABLY_API_CODE
                ]
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $attempts += 1;
                sleep(1);
            }
        } while ($statusCode !== 200 && $attempts <= self::MAX_FETCH_TOKEN_ATTEMPTS);

        $token = $response->json()['token'] ?? 'EMPTY_TOKEN';

        return new TokenResponseDto($statusCode, $token);
    }

    public function getOrdersByStatus(int $page = 1)
    {

    }

    public function getAssignedOrdersByDevice(string $deviceBrand = 'iPhone', int $page = 1)
    {

    }
}
