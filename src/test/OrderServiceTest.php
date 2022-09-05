<?php

declare(strict_types=1);

namespace App\Test;

use App\Dto\OrdersListDto;
use App\Model\Order;
use App\Service\OrderService;
use App\Test\Mock\GuzzleClientMock;
use App\Test\Mock\TokenManagerMock;
use DateTime;
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

        $orderService = $this->getOrderServiceMock($expectedStatusCode, $expectedResponse);
        $response = $orderService->getAssignedOrdersByDevice('iPhone');

        static::assertEquals($expectedStatusCode, $response->getStatusCode());
        static::assertEquals($this->expectedGetAssignedOrdersByDevice()->getOrders(), $response->getOrders());
    }

    /**
     * @test
     * @return void
     */
    public function testCreateOrder(): void
    {
        $expectedOrderId = 17489;

        $expectedResponse = [
            "message" => 'Order created',
            "id" => $expectedOrderId,
        ];

        $order = new Order();
        $order->setDeviceManufacturer('Apple');
        $order->setDeviceBrand('iPhone X');
        $order->setDeviceType('Phone');
        $order->setStatus(1);
        $order->setCreated(new DateTime());

        $expectedStatusCode = 200;

        $orderService = $this->getOrderServiceMock($expectedStatusCode, $expectedResponse);
        $response = $orderService->createOrder($order);

        $expectedMessage = sprintf('Order %s created', $order->getId());

        static::assertEquals($expectedOrderId, $response->getOrder()->getId());
        static::assertEquals($expectedMessage, $response->getMessage());
        static::assertEquals($expectedStatusCode,$response->getStatusCode());
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

        $orderService = $this->getOrderServiceMock($expectedStatusCode, $expectedResponse);
        $response = $orderService->getOrdersByStatus();

        static::assertEquals($expectedStatusCode, $response->getStatusCode());
        static::assertEquals($this->expectedGetOrderByStatus()->getOrders(), $response->getOrders());
    }

    /**
     * @param int   $expectedStatusCode
     * @param array $expectedResponse
     *
     * @return OrderService
     */
    public function getOrderServiceMock(int $expectedStatusCode, array $expectedResponse): OrderService
    {
        $mockedGuzzleClient = GuzzleClientMock::getGuzzleClient($expectedStatusCode, $expectedResponse);
        $mockedTokenManager = TokenManagerMock::getTokenManager();

        return new OrderService($mockedGuzzleClient, $mockedTokenManager);
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
}
