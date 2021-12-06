<?php

return [
    'config' => [
        'doctrine' => [
            'subscribers' => [
                \App\Data\Doctrine\FixDefaultSchemaSubscriber::class,
            ]
        ]
    ]
];