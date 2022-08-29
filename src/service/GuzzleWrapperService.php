<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class GuzzleWrapperService
{
    /**
     * @var Client
     */
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param array|Url|string $url
     * @param array            $options
     *
     * @return void
     */
    public function post($url = null, array $options = []): Response
    {
        return $this->client->post($url, $options);
    }

    /**
     * @param array|Url|string $url
     * @param array            $options
     *
     * @return void
     */
    public function get($url = null, array $options = []): Response
    {
        return $this->client->get($url, $options);
    }


    /**
     * @param Response $response
     *
     * @return int
     */
    public function getStatusCode(Response $response): int
    {
        return $response->getStatusCode();
    }

    /**
     * @param Response $response
     *
     * @return mixed|null
     */
    public function json(Response $response)
    {
        return $response->json();
    }
}
