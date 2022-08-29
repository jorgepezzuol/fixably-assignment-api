<?php

declare(strict_types=1);

namespace App\Test;

use App\Service\FixablyApiService;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../vendor/autoload.php';

class FixablyApiServiceTest extends TestCase
{
    /**
     * @test
     */
    public function testFetchToken()
    {
        $expectedToken = '60ac0d24f69ab294f81b41a5';
        $expectedStatusCode = 200;
        $expectedResponse = [
            'token' => $expectedToken
        ];

        $service = $this->getFixablyApiService($expectedStatusCode, $expectedResponse);

        $response = $service->fetchToken();

        static::assertEquals($expectedStatusCode, $response->getStatusCode());
        static::assertEquals($expectedToken, $response->getToken());
    }

    /**
     * @param int   $expectedStatusCode
     * @param array $expectedResponse
     *
     * @return FixablyApiService
     */
    public function getFixablyApiService(int $expectedStatusCode, array $expectedResponse): FixablyApiService
    {
        $mockedGuzzleClient = $this->getMockedGuzzleClient($expectedStatusCode, $expectedResponse);

        return new FixablyApiService($mockedGuzzleClient);
    }

    /**
     * @param int   $expectedStatusCode
     * @param array $expectedResponse
     *
     * @return Client
     */
    public function getMockedGuzzleClient(int $expectedStatusCode, array $expectedResponse): Client
    {
        return new class ($expectedStatusCode, $expectedResponse) extends Client {
            /**
             * @var int
             */
            private int $statusCode;

            /**
             * @var array
             */
            private array $response;

            public function __construct(int $statusCode, array $response)
            {
                $this->statusCode = $statusCode;
                $this->response = $response;
            }

            /**
             * @param       $url
             * @param array $options
             *
             * @return Response
             */
            public function post($url = null, array $options = []): Response
            {
                $json = json_encode($this->response);
                $body = Stream::factory($json);

                return new Response($this->statusCode, ['Content-Type' => 'application/json'], $body);
            }

            /**
             * @param       $url
             * @param array $options
             *
             * @return Response
             */
            public function get($url = null, $options = []): Response
            {
                $json = json_encode($this->response);
                $body = Stream::factory($json);

                return new Response($this->statusCode, ['Content-Type' => 'application/json'], $body);
            }
        };
    }

}
