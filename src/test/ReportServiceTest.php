<?php

declare(strict_types=1);

namespace App\Test;

use App\Service\ReportService;
use App\Test\Mock\GuzzleClientMock;
use App\Test\Mock\TokenManagerMock;
use DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../vendor/autoload.php';

class ReportServiceTest extends TestCase
{

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function testGenerateGrowthRreport(): void
    {
        $mockedResponse = file_get_contents("./mock/report.json");

        $expectedStatusCode = 200;
        $expectedResponse = json_decode($mockedResponse, true);

        $reportService = $this->getReportServiceMock($expectedStatusCode, $expectedResponse);
        $response = $reportService->generateGrowthReport(new DateTime('2020-11-01'), new DateTime('2020-11-30'));

        static::assertEquals($expectedStatusCode, $response->getStatusCode());
        static::assertEquals($this->getExpectedGrowtReport(), $response->getReport());
    }

    /**
     * @param int   $expectedStatusCode
     * @param array $expectedResponse
     *
     * @return ReportService
     */
    public function getReportServiceMock(int $expectedStatusCode, array $expectedResponse): ReportService
    {
        $mockedGuzzleClient = GuzzleClientMock::getGuzzleClient($expectedStatusCode, $expectedResponse);
        $mockedTokenManager = TokenManagerMock::getTokenManager();

        return new ReportService($mockedGuzzleClient, $mockedTokenManager);
    }

    /**
     * @return array
     */
    public function getExpectedGrowtReport(): array
    {
        return json_decode(
            '{
                  "invoices": 307,
                  "invoicedAmount": 70946.93,
                  "weeks": {
                    "2020-11-02": {
                      "invoices": 77,
                      "invoicedAmount": 18454.23
                    },
                    "2020-11-09": {
                      "invoices": 82,
                      "invoicedAmount": 21604.18,
                      "invoicesQuantityGrowth": "6.5%",
                      "invoicedAmountGrowth": "17.1%"
                    },
                    "2020-11-16": {
                      "invoices": 75,
                      "invoicedAmount": 15344.25,
                      "invoicesQuantityGrowth": "-8.5%",
                      "invoicedAmountGrowth": "-29%"
                    },
                    "2020-11-23": {
                      "invoices": 73,
                      "invoicedAmount": 15544.27,
                      "invoicesQuantityGrowth": "-2.7%",
                      "invoicedAmountGrowth": "1.3%"
                    }
                  }
            }', true
        );
    }
}
