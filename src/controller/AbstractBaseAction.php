<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractBaseAction
{
    /**
     * @param ResponseInterface $httpResponse
     * @param array             $response
     *
     * @return ResponseInterface
     */
    abstract protected function writeJsonResponse(ResponseInterface $httpResponse, array $response): ResponseInterface;

    /**
     * @return array
     */
    abstract protected function getErrorResponse(): array;
}
