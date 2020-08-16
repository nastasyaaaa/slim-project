<?php

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Doctrine\ORM\Tools\Console\ConsoleRunner as ORMConsoleRunner;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Tools\Console\ConsoleRunner as MigrationsConsoleRunner;

require __DIR__ . '/../vendor/autoload.php';

Sentry\init(['dsn' => 'https://f03f134abcf44defafdac6b38b238475@o434717.ingest.sentry.io/5392124' ]);

$cli = new Application('Console');

/** @var ContainerInterface $container */
$container = (require __DIR__ . '/../config/container.php')();

/** Add custom commands to cli app */
$commands = $container->get('config')['console']['commands'];
foreach ($commands as $command) {
    $cli->add($container->get($command));
}

/** Add EM to cli app */
$entityManager = $container->get(EntityManagerInterface::class);
$cli->getHelperSet()->set(new EntityManagerHelper($entityManager), 'em');

/** Add Doctrine ORM commands to cli app */
ORMConsoleRunner::addCommands($cli);

/** Add Doctrine migration commands */
$config = new PhpFile(__DIR__ . '/../config/console/migrations.php');
$dependencyFactory = DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));

MigrationsConsoleRunner::addCommands($cli, $dependencyFactory);


$cli->run();