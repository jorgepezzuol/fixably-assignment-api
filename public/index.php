<?php

declare(strict_types=1);

use App\Controller\CreateOrderWithIssueAction;
use App\Controller\GenerateReportAction;
use App\Controller\GetAssignedOrdersByDeviceAction;
use App\Controller\GetOrdersByStatusAction;
use App\Dto\OrderNoteDto;
use App\Service\TokenService;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = AppFactory::create();

$app->get('/orders', GetOrdersByStatusAction::class);
$app->get('/orders/assigned', GetAssignedOrdersByDeviceAction::class);
$app->get('/orders/create/issue', CreateOrderWithIssueAction::class);
$app->get('/reports', GenerateReportAction::class);

$app->run();
