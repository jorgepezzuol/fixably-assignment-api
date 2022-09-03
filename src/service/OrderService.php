<?php

declare(strict_types=1);

namespace App\Service;

use App\Auth\TokenManager;
use App\Dto\NoteDto;
use App\Dto\OrderDto;
use App\Dto\OrderNoteDto;
use App\Dto\OrdersListDto;
use App\Dto\ReportDto;
use App\Enum\FixablyEnum;
use App\Helper\NoteHelper;
use App\Helper\OrderHelper;
use App\Model\Order;
use App\Model\Note;
use DateInterval;
use DateTime;
use Exception;
use GuzzleHttp\Client;

class OrderService
{
    /**
     * @var Client
     */
    private Client $guzzleClient;

    /**
     * @var TokenManager
     */
    private TokenManager $tokenManager;

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
     * @param int $page
     *
     * @return OrdersListDto
     */
    public function getOrdersByStatus(int $page = 1): OrdersListDto
    {
        $endpoint = sprintf('%s/orders?page=%s', FixablyEnum::API_URL, 33);

        $response = $this->guzzleClient->get($endpoint, $this->tokenManager->createHeaders());

        $sortedOrders = OrderHelper::sortOrdersByStatus($response->json());

        return new OrdersListDto($response->getStatusCode(), $page, $sortedOrders);
    }

    /**
     * @param string $device
     * @param int    $page
     *
     * @return OrdersListDto
     */
    public function getAssignedOrdersByDevice(string $device = 'iPhone', int $page = 1): OrdersListDto
    {
        $endpoint = sprintf('%s/search/devices?page=%s', FixablyEnum::API_URL, 1);

        $requestBody = [
            'body' => [
                'Criteria' => $device
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->tokenManager->createHeaders(), $requestBody
        ));

        $assingedOrders = OrderHelper::filterByAssignedOrders($response->json());

        return new OrdersListDto($response->getStatusCode(), $page, $assingedOrders);
    }

    /**
     * @param Order $order
     *
     * @return OrderDto
     */
    public function createOrder(Order $order): OrderDto
    {
        if (!OrderHelper::isOrderValid($order)) {
            return new OrderDto($order, 400, implode(', ', $order->getErrors()));
        }

        $endpoint = sprintf('%s/orders/create', FixablyEnum::API_URL, 1);

        $requestBody = [
            'body' => [
                'DeviceManufacturer' => $order->getDeviceManufacturer(),
                'DeviceBrand' => $order->getDeviceBrand(),
                'DeviceType' => $order->getDeviceType(),
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->tokenManager->createHeaders(), $requestBody
        ));

        $message = 'Error while creating order';

        if ($response->getStatusCode() === 200 && isset($response->json()['id'])) {
            $order->setId($response->json()['id']);
            $message = sprintf('Order %s created', $order->getId());
        }

        return new OrderDto($order, $response->getStatusCode(), $message);
    }

    /**
     * @param Note $note
     *
     * @return NoteDto
     * @throws Exception
     */
    public function createNote(Note $note): NoteDto
    {
        if (!NoteHelper::isNoteValid($note)) {
            return new NoteDto($note, 400, implode(', ', $note->getErrors()));
        }

        $endpoint = sprintf('%s/orders/%s/notes/create', FixablyEnum::API_URL, $note->getOrderId());

        $requestBody = [
            'body' => [
                'Type' => $note->getType(),
                'Description' => $note->getDescription(),
            ]
        ];

        $response = $this->guzzleClient->post($endpoint, array_merge(
            $this->tokenManager->createHeaders(), $requestBody
        ));

        $message = 'Error while creating order';

        if ($response->getStatusCode() === 200 && isset($response->json()['id'])) {
            $note->setId($response->json()['id']);
            $message = sprintf('Note %s created', $note->getId());
        }

        return new NoteDto($note, $response->getStatusCode(), $message);
    }

    /**
     * TODO: validate range (at least 1 week distance) - validate dates
     *
     * @param DateTime $fromDate
     * @param DateTime $toDate
     *
     * @return ReportDto
     * @throws Exception
     */
    public function generateWeeklyReport(DateTime $fromDate, DateTime $toDate): ReportDto
    {
        $endpoint = sprintf('%s/report/%s/%s', FixablyEnum::API_URL,
            $fromDate->format('Y-m-d'),
            $toDate->format('Y-m-d')
        );

        $weeklyReport = [
            'totalInvoices' => 0,
            'totalInvoicedAmount' => 0.0,
            'weeks' => []
        ];

        $page = 1;

        do {
            $url = sprintf('%s?page=%s', $endpoint, $page);
            $response = $this->guzzleClient->post($url, $this->tokenManager->createHeaders());
            $results = $response->json()['results'] ?? null;

            if ($results !== null) {
                foreach ($results as $result) {
                    $weeklyReport['totalInvoices'] += 1;
                    $weeklyReport['totalInvoicedAmount'] += $result['amount'];

                    $createdDateTime = new DateTime($result['created']);
                    $weekNumber = $createdDateTime->format('W');
                    $year = $createdDateTime->format('Y');

                    $weekStartDate = (new DateTime())->setISODate((int)$year, (int)$weekNumber)->format('Y-m-d');

                    if (!isset($weeklyReport['weeks'][$weekStartDate])) {
                        $weeklyReport['weeks'][$weekStartDate] = [
                            'totalInvoices' => 0,
                            'totalInvoicedAmount' => 0.0
                        ];
                    }

                    $weeklyReport['weeks'][$weekStartDate]['totalInvoices'] += 1;
                    $weeklyReport['weeks'][$weekStartDate]['totalInvoicedAmount'] += $result['amount'];
                }
            }

            $page += 1;

        } while ($response->getStatusCode() === 200 && $results !== null);

        $weeks = $weeklyReport['weeks'];
        $keys = array_keys($weeks);

        for ($i = 0; $i < count($keys) - 1; $i++) {
            $current = $weeks[$keys[$i]];
            $next = $weeks[$keys[$i + 1]] ?? false;

            if ($next !== false) {
                $nextInvoices = $next['totalInvoices'];
                $nextAmount = $next['totalInvoicedAmount'];

                $prevInvoices = $current['totalInvoices'];
                $prevAmount = $current['totalInvoicedAmount'];

                $percentageInvoices = round(($nextInvoices - $prevInvoices) / $prevInvoices * 100, 1);
                $percentageAmount = round(($nextAmount - $prevAmount) / $prevAmount * 100, 1);

                $weeklyReport['weeks'][$keys[$i + 1]]['totalInvoiceGrowth'] = sprintf('%s%s', $percentageInvoices, '%');
                $weeklyReport['weeks'][$keys[$i + 1]]['totalInvoiceAmountGrowth'] = sprintf('%s%s', $percentageAmount, '%');
            }
        }

        echo '<pre>';
        print_r($weeklyReport);
        exit;

        return new ReportDto();
    }
}
