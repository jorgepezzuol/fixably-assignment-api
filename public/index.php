<?php

declare(strict_types=1);

use App\Service\FixablyApiService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Register middleware
//$app->addRoutingMiddleware();
//$app->addBodyParsingMiddleware();
//$app->addErrorMiddleware(true, true, true);

$app->get('/[{name}]', function (
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {

    try {
        // TODO: factory
        $service = new FixablyApiService(new Client());
        $tokenResponseDto = $service->fetchToken();

        $response->getBody()->write(json_encode(
            [
                'status' => $tokenResponseDto->getStatusCode(),
                'data' => [
                    'token' => $tokenResponseDto->getToken()
                ]
            ]
        ));
    } catch (Exception $exception) {

    }

    return $response->withHeader('Content-type', 'application/json');;
});

$app->run();
