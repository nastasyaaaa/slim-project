<?php

use Psr\Container\ContainerInterface;

return [
    \App\Auth\Service\IJoinConfirmationSender::class => Di\get(\App\Auth\Service\JoinConfirmationSender::class),

    \App\Auth\Service\JoinConfirmationSender::class => static function (ContainerInterface $container) {

        $config = $container->get('config')['mailer'];

        /** @var Swift_Mailer $mailer */
        $mailer = $container->get(Swift_Mailer::class);

        return new \App\Auth\Service\JoinConfirmationSender($mailer, $config['from']);
    }
];