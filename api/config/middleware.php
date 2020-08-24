<?php

use Slim\App;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use App\Http\Middleware\DomainExceptionHandlerMiddleware;
use App\Http\Middleware\ClearEmptyInputMiddleware;
use App\Http\Middleware\ValidationExceptionHandler;

return static function (App $app, Container $container) {
    $app->add(DomainExceptionHandlerMiddleware::class);
    $app->add(ValidationExceptionHandler::class);
    $app->add(ClearEmptyInputMiddleware::class);
    $app->add(ErrorMiddleware::class);
    $app->addBodyParsingMiddleware();
};