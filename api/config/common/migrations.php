<?php

use Doctrine\Migrations;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command;
use Doctrine\Migrations\Configuration\Configuration;

return [
    DependencyFactory::class => static function (ContainerInterface $container) {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);

        $config = new Configuration();
        $config->addMigrationsDirectory('App\Data\Migration', __DIR__ . '/../../src/Data/Migration');
        $config->setAllOrNothing(true);
        $config->setCheckDatabasePlatform(true);

        $storageConfig = new Migrations\Metadata\Storage\TableMetadataStorageConfiguration();
        $storageConfig->setTableName('migrations');
        $storageConfig->setVersionColumnName('version');
        $storageConfig->setVersionColumnLength(1024);
        $storageConfig->setExecutedAtColumnName('executed_at');
        $storageConfig->setExecutionTimeColumnName('execution_time');

        $config->setMetadataStorageConfiguration($storageConfig);

        return DependencyFactory::fromEntityManager(
            new Migrations\Configuration\Migration\ExistingConfiguration($config),
            new Migrations\Configuration\EntityManager\ExistingEntityManager($entityManager)
        );
    },
    Command\ExecuteCommand::class => static function (ContainerInterface $container) {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get(DependencyFactory::class);
        return new Command\ExecuteCommand($dependencyFactory);
    },
    Command\MigrateCommand::class => static function (ContainerInterface $container) {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get(DependencyFactory::class);
        return new Command\MigrateCommand($dependencyFactory);
    },
    Command\LatestCommand::class => static function (ContainerInterface $container) {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get(DependencyFactory::class);
        return new Command\LatestCommand($dependencyFactory);
    },
    Command\ListCommand::class => static function (ContainerInterface $container) {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get(DependencyFactory::class);
        return new Command\ListCommand($dependencyFactory);
    },
    Command\StatusCommand::class => static function (ContainerInterface $container) {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get(DependencyFactory::class);
        return new Command\StatusCommand($dependencyFactory);
    },
    Command\UpToDateCommand::class => static function (ContainerInterface $container) {
        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = $container->get(DependencyFactory::class);
        return new Command\UpToDateCommand($dependencyFactory);
    },
];