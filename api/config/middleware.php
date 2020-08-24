<?php

use Slim\App;
use DI\Container;
use Middlewares\ContentLanguage;
use Slim\Middleware\ErrorMiddleware;
use App\Http\Middleware\ClearEmptyInputMiddleware;
use App\Http\Middleware\ValidationExceptionHandler;
use App\Http\Middleware\TranslationLocaleMiddleware;
use App\Http\Middleware\DomainExceptionHandlerMiddleware;

return static function (App $app, Container $container) {
    $app->add(DomainExceptionHandlerMiddleware::class);
    $app->add(ValidationExceptionHandler::class);
    $app->add(ClearEmptyInputMiddleware::class);
    $app->add(TranslationLocaleMiddleware::class);
    $app->add(ContentLanguage::class);
    $app->add(ErrorMiddleware::class);
    $app->addBodyParsingMiddleware();
};