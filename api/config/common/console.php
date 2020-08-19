<?php

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Console\Commands\MailerCheckCommand;
use App\Console\Commands\FixturesLoadCommand;

return [
    FixturesLoadCommand::class => static function (ContainerInterface $container) {
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        $paths = $container->get('config')['console']['fixtures_paths'];

        return new FixturesLoadCommand($em, $paths);
    },


    'config' => [
        'console' =>
            [
                'commands' => [
                    FixturesLoadCommand::class,
                    MailerCheckCommand::class,
                ],

                'fixtures_paths' => [
                    __DIR__ . '/../../src/Auth/Fixture'
                ]
            ],
    ],
];