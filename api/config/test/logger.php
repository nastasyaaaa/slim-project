<?php

return [
    'config' => [
        'logger' => [
            'stderr' => false,
            'file' => __DIR__ . '/../../var/log/' . PHP_SAPI . '-test' . '/application.log'
        ]
    ]
];
