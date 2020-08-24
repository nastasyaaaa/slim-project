<?php

use Psr\Container\ContainerInterface;

return [
    \App\Auth\Service\IJoinConfirmationSender::class => Di\get(\App\Auth\Service\JoinConfirmationSender::class),

    \App\Auth\Service\JoinConfirmationSender::class => static function (ContainerInterface $container) {

        $config = $container->get('config')['mailer'];

        /** @var Swift_Mailer $mailer */
        $mailer = $container->get(Swift_Mailer::class);

        return new \App\Auth\Service\JoinConfirmationSender($mailer, $config['from']);
    },
    \App\Auth\Service\Tokenizer::class => static function (ContainerInterface $container) {
        $interval = $container->get('config')['auth']['default_tokenizer_interval'];

        return new \App\Auth\Service\Tokenizer(new DateInterval($interval));
    },

    'config' => [
        'auth' => [
            'default_tokenizer_interval' => 'P2D',
        ]
    ]
];