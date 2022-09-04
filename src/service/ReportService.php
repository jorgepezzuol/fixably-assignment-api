<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ReportDto;
use DateTime;
use Exception;

class ReportService extends AbstractBaseService
{
    /**
     * @param DateTime $fromDate
     * @param DateTime $toDate
     *
     * @return ReportDto
     * @throws Exception
     */
    public function generateGrowthRreport(DateTime $fromDate, DateTime $toDate): ReportDto
    {
        $endpoint = sprintf('%s/report/%s/%s', self::FIXABLY_API_URL,
            $fromDate->format('Y-m-d'),
            $toDate->format('Y-m-d')
        );

        $report = [
            'invoices' => 0,
            'invoicedAmount' => 0.0,
            'weeks' => []
        ];

        $page = 1;

        do {
            $url = sprintf('%s?page=%s', $endpoint, $page);
            $response = $this->guzzleClient->post($url, $this->createHeaders());
            $results = $response->json()['results'] ?? [];

            foreach ($results as $result) {
                $report['invoices'] += 1;
                $report['invoicedAmount'] += $result['amount'];
                $report['invoicedAmount'] = round($report['invoicedAmount'], 2);

                $createdDateTime = new DateTime($result['created']);
                $weekNumber = $createdDateTime->format('W');
                $year = $createdDateTime->format('Y');

                $weekStartDate = (new DateTime())->setISODate((int)$year, (int)$weekNumber)->format('Y-m-d');

                if (!isset($report['weeks'][$weekStartDate])) {
                    $report['weeks'][$weekStartDate] = [
                        'invoices' => 0,
                        'invoicedAmount' => 0.0
                    ];
                }

                $weeks = &$report['weeks'][$weekStartDate];

                $weeks['invoices'] += 1;
                $weeks['invoicedAmount'] += $result['amount'];
                $weeks['invoicedAmount'] = round($report['weeks'][$weekStartDate]['invoicedAmount'], 2);
            }

            $page += 1;
            $statusCode = $response->getStatusCode();

        } while ($statusCode === 200 && count($results) > 0);

        $message = 'Failure while generating report';

        if ($statusCode === 200) {
            $message = 'OK';
            $this->calculateWeeklyGrowth($report['weeks']);
        }

        return new ReportDto($statusCode, $report, $message);
    }

    /**
     * @param array &$report
     *
     * @return void
     */
    private function calculateWeeklyGrowth(array &$report): void
    {
        $keys = array_keys($report);

        for ($i = 0; $i < count($keys) - 1; $i++) {
            $current = $report[$keys[$i]];
            $next = $report[$keys[$i + 1]] ?? false;

            if ($next !== false) {
                $currentInvoices = $current['invoices'];
                $currentAmount = $current['invoicedAmount'];

                $nextInvoices = $next['invoices'];
                $nextAmount = $next['invoicedAmount'];

                $percentageInvoices = round(($nextInvoices - $currentInvoices) / $currentInvoices * 100, 1);
                $percentageAmount = round(($nextAmount - $currentAmount) / $currentAmount * 100, 1);

                $report[$keys[$i + 1]]['invoicesQuantityGrowth'] = sprintf('%s%s', $percentageInvoices, '%');
                $report[$keys[$i + 1]]['invoicedAmountGrowth'] = sprintf('%s%s', $percentageAmount, '%');
            }
        }
    }
}
