<?php

use Doctrine\Migrations;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\Tools\Console\Command\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;

return [
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