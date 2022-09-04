<?php

declare(strict_types=1);

namespace App\Service;

use App\Auth\TokenManager;
use GuzzleHttp\Client;

abstract class AbstractBaseService
{
    protected const FIXABLY_HEADER = 'X-Fixably-Token';
    protected const FIXABLY_API_URL = 'https://careers-api.fixably.com';

    /**
     * @var TokenManager
     */
    private TokenManager $tokenManager;

    /**
     * @var Client
     */
    protected Client $guzzleClient;

    /**
     * @param Client       $guzzleClient
     * @param TokenManager $tokenManager
     */
    public function __construct(Client $guzzleClient, TokenManager $tokenManager)
    {
        $this->guzzleClient = $guzzleClient;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @return array
     */
    protected function createHeaders(): array
    {
        return [
            'headers' => [
                self::FIXABLY_HEADER => $this->tokenManager->fetchToken()
            ]
        ];
    }
}
