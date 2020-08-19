<?php

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Psr\Container\ContainerInterface;
use Monolog\Handler\RotatingFileHandler;

return [
    LoggerInterface::class => static function (ContainerInterface $container) {

        $config = $container->get('config')['logger'];

        $level = $config['debug'] ? Logger::DEBUG : Logger::INFO;

        $logger = new Logger('API');

        if ($config['stderr']) {
            $logger->pushHandler(new StreamHandler('php://stderr', $level));
        }

        if (!empty($config['file'])) {
            $logger->pushHandler(new RotatingFileHandler($config['file'], $level));
        }

        return $logger;
    },

    'config' => [
        'logger' => [
            'debug' => (bool)getenv('APP_DEBUG'),
            'file' => null, //depends on env
            'stderr' => true,
        ]
    ]
];