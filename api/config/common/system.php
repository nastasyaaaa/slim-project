<?php

return [
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG'),
        'env' => getenv('APP_ENV'),
    ],
    \Psr\Http\Message\ResponseFactoryInterface::class => Di\get(\Slim\Psr7\Factory\ResponseFactory::class),
    \App\IFlusher::class => \App\Flusher::class,
];