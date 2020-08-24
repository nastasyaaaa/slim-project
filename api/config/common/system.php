<?php

use App\Flusher;
use App\IFlusher;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Message\ResponseFactoryInterface;

return [
    ResponseFactoryInterface::class => Di\get(ResponseFactory::class),
    IFlusher::class => Di\get(Flusher::class),

    'config' => [
        'debug' => (bool)getenv('APP_DEBUG'),
        'env' => getenv('APP_ENV') ?: 'prod',
    ],
];