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

class GenerateReportAction extends AbstractBaseAction
{
    /**
     * @param ServerRequestInterface $httpRequest
     * @param ResponseInterface      $httpResponse
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $httpRequest, ResponseInterface $httpResponse): ResponseInterface
    {
        try {
            $reportService = new ReportService(new Client(), new TokenManager());
            $response = $reportService->generateGrowthReport(new DateTime('2020-11-01'), new DateTime('2020-11-30'));

            $arrayResponse = [
                'status' => $response->getStatusCode(),
                'message' => $response->getMessage(),
                'data' => [
                    'report' => $response->getReport()
                ]
            ];

        } catch (Exception $exception) {
            $arrayResponse = $this->getErrorResponse();
        }

        return $this->writeJsonResponse($httpResponse, $arrayResponse);
    }

    /**
     * @param ResponseInterface $httpResponse
     * @param array             $response
     *
     * @return ResponseInterface
     */
    protected function writeJsonResponse(ResponseInterface $httpResponse, array $response): ResponseInterface
    {
        $json = json_encode($response);

        $httpResponse->getBody()->write($json);

        return $httpResponse->withStatus($response['status'])->withHeader('Content-type', 'application/json');
    }

    /**
     * @return array
     */
    protected function getErrorResponse(): array
    {
        return [
            'status' => 500,
            'message' => 'Error while generating report',
            'data' => []
        ];
    }
}
