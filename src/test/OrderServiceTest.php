<?php

declare(strict_types=1);

namespace App\Test;

use App\Auth\TokenManager;
use App\Dto\OrdersListDto;
use App\Service\OrderService;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../vendor/autoload.php';

class OrderServiceTest extends TestCase
{

    /**
     * @test
     * @dataProvider provideOrders
     *
     * @param int   $page
     * @param int   $total
     * @param array $results
     *
     * @return void
     */
    public function testGetAssignedOrdersByDevice(int $page, int $total, array $results): void
    {
        $expectedResponse = [
            "page" => $page,
            "total" => $total,
            "results" => $results
        ];

        $expectedStatusCode = 200;

        $orderService = $this->getMockedOrderService($expectedStatusCode, $expectedResponse);
        $response = $orderService->getAssignedOrdersByDevice('iPhone');

        static::assertEquals($expectedStatusCode, $response->getStatusCode());
        static::assertEquals($this->expectedGetAssignedOrdersByDevice()->getOrders(), $response->getOrders());
    }

    public function testCreateOrder()
    {

    }

    /**
     * @test
     * @dataProvider provideOrders
     *
     * @param int   $page
     * @param int   $total
     * @param array $results
     *
     * @return void
     */
    public function testGetOrdersByStatus(int $page, int $total, array $results): void
    {
        $expectedResponse = [
            "page" => $page,
            "total" => $total,
            "results" => $results
        ];

        $expectedStatusCode = 200;

        $orderService = $this->getMockedOrderService($expectedStatusCode, $expectedResponse);
        $response = $orderService->getOrdersByStatus();

        static::assertEquals($expectedStatusCode, $response->getStatusCode());
        static::assertEquals($this->expectedGetOrderByStatus()->getOrders(), $response->getOrders());
    }

    /**
     * @return array
     */
    public function provideOrders(): iterable
    {
        yield 'response with orders' => [
            'page' => 1,
            'total' => 2,
            'results' => [
                [
                    "id" => 9243,
                    "deviceType" => "Laptop",
                    "deviceManufacturer" => "Apple",
                    "deviceBrand" => "iPhone X",
                    "technician" => "Pasi",
                    "status" => 3,
                    "created" => "2020-10-01 10:05:57"
                ],
                [
                    "id" => 9244,
                    "deviceType" => "Laptop",
                    "deviceManufacturer" => "Apple",
                    "deviceBrand" => "iPhone 8",
                    "technician" => null,
                    "status" => 4,
                    "created" => "2020-10-01 10:10:42"
                ],
            ]
        ];
    }

    /**
     * @return OrdersListDto
     */
    public function expectedGetOrderByStatus(): OrdersListDto
    {
        return new OrdersListDto(200, [
            'Assigned' => [
                'amount' => 1,
                'average' => '50%',
                'orders' => [
                    0 => [
                        "id" => 9243,
                        "deviceType" => "Laptop",
                        "deviceManufacturer" => "Apple",
                        "deviceBrand" => "iPhone X",
                        "technician" => "Pasi",
                        "status" => 'Assigned',
                        "created" => "2020-10-01 10:05:57"
                    ]
                ],
            ],
            'Unpaid' => [
                'amount' => 1,
                'average' => '50%',
                'orders' => [
                    0 => [
                        "id" => 9244,
                        "deviceType" => "Laptop",
                        "deviceManufacturer" => "Apple",
                        "deviceBrand" => "iPhone 8",
                        "technician" => null,
                        "status" => 'Unpaid',
                        "created" => "2020-10-01 10:10:42"
                    ]
                ]
            ]
        ]);
    }

    /**
     * @return OrdersListDto
     */
    public function expectedGetAssignedOrdersByDevice(): OrdersListDto
    {
        return new OrdersListDto(200, [
                0 => [
                    "id" => 9243,
                    "deviceType" => "Laptop",
                    "deviceManufacturer" => "Apple",
                    "deviceBrand" => "iPhone X",
                    "technician" => "Pasi",
                    "status" => 'Assigned',
                    "created" => "2020-10-01 10:05:57"
                ]
            ]
        );
    }

    /**
     * @param int   $expectedStatusCode
     * @param array $expectedResponse
     *
     * @return OrderService
     */
    public function getMockedOrderService(int $expectedStatusCode, array $expectedResponse): OrderService
    {
        $mockedGuzzleClient = $this->getMockedGuzzleClient($expectedStatusCode, $expectedResponse);
        $mockedTokenManager = $this->getMockedTokenManager();

        return new OrderService($mockedGuzzleClient, $mockedTokenManager);
    }

    /**
     * @return TokenManager
     */
    public function getMockedTokenManager(): TokenManager
    {
        return new class () extends TokenManager {
            private const EXPECTED_TOKEN = 'd16f6506f45cd0f1d8173bfd';

            /**
             * @return string
             */
            public function fetchToken(): string
            {
                return self::EXPECTED_TOKEN;
            }
        };
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
