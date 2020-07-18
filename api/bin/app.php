<?php

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

require __DIR__ . '/../vendor/autoload.php';

$cli = new Application('Console');

/** @var ContainerInterface $container */
$container = (require __DIR__ . '/../config/container.php')();

$commands = $container->get('config')['console']['commands'];

// Add EM to cli app
$entityManager = $container->get(EntityManagerInterface::class);
$cli->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');

// Add Doctrine ORM commands to cli app
ConsoleRunner::addCommands($cli);

foreach($commands as $command) {
    $cli->add($container->get($command));
}

$cli->run();