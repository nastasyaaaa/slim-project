<?php

return [
    'config' => [
        'console' => [
            'commands' => [
                \App\Console\Commands\FixturesLoadCommand::class,
            ],
            'fixtures_paths' => [
                __DIR__ . '/../../src/Auth/Fixture',
            ]
        ]
    ]
];