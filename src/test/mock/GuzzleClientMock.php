<?php

declare(strict_types=1);

namespace App\Test\Mock;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class GuzzleClientMock
{
    /**
     * @param int   $expectedStatusCode
     * @param array $expectedResponse
     *
     * @return Client
     */
    public static function getGuzzleClient(int $expectedStatusCode, array $expectedResponse): Client
    {
        return new class ($expectedStatusCode, $expectedResponse) extends Client {

            /**
             * @var int
             */
            private int $statusCode;

            /**
             * @var int
             */
            private int $fakePagination = 0;

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
                $body = null;

                if ($this->fakePagination === 0) {
                    $json = json_encode($this->response);
                    $body = Stream::factory($json);
                    $this->fakePagination += 1;
                }

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
                $body = null;

                if ($this->fakePagination === 0) {
                    $json = json_encode($this->response);
                    $body = Stream::factory($json);
                    $this->fakePagination += 1;
                }

                return new Response($this->statusCode, ['Content-Type' => 'application/json'], $body);
            }
        };
    }
}
