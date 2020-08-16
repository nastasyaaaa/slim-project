<?php

use Psr\Log\LoggerInterface;
use Slim\Middleware\ErrorMiddleware;
use App\ErrorHandler\LogErrorHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;

return [
    ErrorMiddleware::class => static function (ContainerInterface $container): ErrorMiddleware {

        /** @var CallableResolverInterface $callableResolver */
        $callableResolver = $container->get(CallableResolverInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        $config = $container->get('config')['errors'];

        $middleware = new ErrorMiddleware(
            $callableResolver,
            $responseFactory,
            $config['display_details'],
            $config['log'],
            true
        );

        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        $middleware->setDefaultErrorHandler(
            new \App\ErrorHandler\SentryDecorator(
                new LogErrorHandler($callableResolver, $responseFactory, $logger)
            )
        );

        return $middleware;
    },

    'config' => [
        'errors' => [
            'display_details' => (bool)getenv('APP_DEBUG'),
            'log' => true
        ]
    ]
];