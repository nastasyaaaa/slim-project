<?php

use Doctrine\Migrations;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand;

return [
    \App\Console\Commands\FixturesLoadCommand::class => static function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);
        $paths = $container->get('config')['console']['fixtures_paths'] ?? [];

        return new \App\Console\Commands\FixturesLoadCommand($em, $paths);
    },
    'config' => [
        'console' => [
            'commands' => [
                ValidateSchemaCommand::class,

                Migrations\Tools\Console\Command\ExecuteCommand::class,
                Migrations\Tools\Console\Command\MigrateCommand::class,
                Migrations\Tools\Console\Command\LatestCommand::class,
                Migrations\Tools\Console\Command\ListCommand::class,
                Migrations\Tools\Console\Command\StatusCommand::class,
                Migrations\Tools\Console\Command\UpToDateCommand::class,
            ]
        ]
    ]
];