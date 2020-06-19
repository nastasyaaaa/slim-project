<?php

use Symfony\Component\Console\Application;
use Psr\Container\ContainerInterface;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application('Console');

/** @var ContainerInterface $container */
$container = (require __DIR__ . '/../src/config/container.php')();

$commands = $container->get('config')['console']['commands'];

foreach($commands as $command) {
    $app->add($container->get($command));
}

$app->run();