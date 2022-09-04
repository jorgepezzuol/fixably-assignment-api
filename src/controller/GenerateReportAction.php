<?php

declare(strict_types=1);

namespace App\Controller;

use App\Auth\TokenManager;
use App\Service\ReportService;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GenerateReportAction
{
    public function __invoke(ServerRequestInterface $httpRequest, ResponseInterface $httpResponse): ResponseInterface {
        try {
            $reportService = new ReportService(new Client(), new TokenManager());
            $response = $reportService->generateGrowthRreport(new DateTime('2020-11-01'), new DateTime('2020-11-30'));

            $httpResponse->getBody()->write(json_encode(
                [
                    'status' => $response->getStatusCode(),
                    'message' => $response->getMessage(),
                    'data' => [
                        'report' => $response->getReport()
                    ]
                ]
            ));

        } catch (Exception $exception) {
            var_dump($exception->getMessage());
        }

        return $httpResponse->withHeader('Content-type', 'application/json');;
    }
}
