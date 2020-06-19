<?php

use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Slim\App;

return static function (ContainerInterface $container) : App {
    $app = AppFactory::createFromContainer($container);
    (require __DIR__ . '/routes.php')($app);
    (require __DIR__ . '/middleware.php')($app, $container);

    return $app;
};