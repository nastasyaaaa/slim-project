<?php

use Doctrine\Migrations;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;

return [
    \App\Console\Commands\FixturesLoadCommand::class => static function (ContainerInterface $container) {
        $em = $container->get(EntityManagerInterface::class);
        $paths = $container->get('config')['console']['fixtures_paths'] ?? [];

        return new \App\Console\Commands\FixturesLoadCommand($em, $paths);
    },
    'config' => [
        'console' => [
            'commands' => [
                \App\Console\Commands\FixturesLoadCommand::class,

                SchemaTool\DropCommand::class,
                SchemaTool\CreateCommand::class,

                Migrations\Tools\Console\Command\DiffCommand::class,
                Migrations\Tools\Console\Command\GenerateCommand::class,
            ],

            'fixtures_paths' => [
                __DIR__ . '/../../src/Auth/Fixture',
            ]
        ]
    ]
];