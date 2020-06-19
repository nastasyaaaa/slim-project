<?php

return [
    'config' => [
        'debug' => (bool)getenv('APP_DEBUG')
    ],
    \Psr\Http\Message\ResponseFactoryInterface::class => Di\get(\Slim\Psr7\Factory\ResponseFactory::class),
];