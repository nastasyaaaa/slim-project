<?php

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;

require __DIR__ . '/../vendor/autoload.php';

Sentry\init(['dsn' => 'https://f03f134abcf44defafdac6b38b238475@o434717.ingest.sentry.io/5392124' ]);

$cli = new Application('Console');

/** @var ContainerInterface $container */
$container = (require __DIR__ . '/../config/container.php')();

/** Add EM to cli app */
$entityManager = $container->get(EntityManagerInterface::class);
$cli->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');

/** Add commands to cli app */
$commands = $container->get('config')['console']['commands'];
foreach ($commands as $command) {
    $cli->add($container->get($command));
}

$cli->run();