<?php

use Slim\App;
use DI\Container;

return static function (App $app, Container $container) {
    $env = $container->get('config')['env'];

    $app->addErrorMiddleware($container->get('config')['debug'], $env !== 'test', $env !== 'test');
};