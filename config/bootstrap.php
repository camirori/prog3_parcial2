<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface;
use Config\Database;

// Instanciar BD Illuminate (database.php)
new Database();

$app = AppFactory::create();
$app->setBasePath("/prog3_parcial2/public");                   //nombre carpeta base, donde se encuentra index.php

$app->addRoutingMiddleware();
$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    //$logger->error($exception->getMessage());
    $payload = ['error' => $exception->getMessage()];
    
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );
    return $response;
};

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);   //para configurar que tipo de error se muestra
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// REGISTRAR RUTAS
(require_once __DIR__ . '/routes.php')($app);

// REGISTRAR MIDDLEWARE
(require_once __DIR__ . '/middlewares.php')($app);

return $app;    //se ejecuta run() en index.php