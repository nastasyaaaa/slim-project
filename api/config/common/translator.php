<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;
use App\Http\Middleware\TranslationLocaleMiddleware;
use Middlewares\ContentLanguage;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;

return [
    TranslatorInterface::class => Di\get(Translator::class),

    Translator::class => static function (ContainerInterface $container): Translator {

        /** @var array $config */
        $config = $container->get('config')['translator'];

        $translator = new Translator($config['locales']['default']);
        $translator->addLoader('php', new PhpFileLoader());
        $translator->addLoader('xlf', new XliffFileLoader());

        foreach ($config['resources'] as $resource) {
            $translator->addResource(...$resource);
        }

        return $translator;
    },

    // parse best locale (preferred by user and accepted by server) from accept-language header and set it to accept-language header
    ContentLanguage::class => static function (ContainerInterface $container): ContentLanguage {
        /** @var array $config */
        $config = $container->get('config')['translator'];

        return new ContentLanguage($config['locales']['allowed']);
    },

    TranslationLocaleMiddleware::class => static function (ContainerInterface $container): TranslationLocaleMiddleware {
        /** @var Translator $translator */
        $translator = $container->get(Translator::class);

        return new TranslationLocaleMiddleware($translator);
    },

    'config' => [
        'translator' => [
            'resources' => [
                [
                    'xlf',
                    __DIR__ . '/../../vendor/symfony/validator/Resources/translations/validators.ru.xlf',
                    'ru',
                    'validators'
                ],
                [
                    'php',
                    __DIR__ . '/../../translations/exceptions.ru.php',
                    'ru',
                    'exceptions'
                ]
            ],
            'locales' => [
                'default' => 'en',
                'allowed' => ['en', 'ru'],
            ]
        ],

    ]
];