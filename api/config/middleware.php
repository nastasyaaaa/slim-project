<?php

use Slim\App;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;

return static function (App $app, Container $container) {
    $app->add(ErrorMiddleware::class);
};