<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\ArrayCache;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

return [
    EntityManagerInterface::class => function (ContainerInterface $container): EntityManagerInterface {

        $settings = $container->get('config')['doctrine'];

        $config = Setup::createAnnotationMetadataConfiguration(
            $settings['metadata_dirs'] ?? [],
            $settings['dev_mode'] ?? false,
            $settings['proxy_dir'] ?? null,
            $settings['cache_dir'] ? new FilesystemCache($settings['cache_dir']) : new ArrayCache(),
            $settings['simple_annotation_reader'] ?? false
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        // Add custom types
        $types = $settings['types'] ?? [];
        foreach ($types as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }


        return EntityManager::create($settings['connection'] ?? [], $config);
    },
    'config' => [
        'doctrine' => [
            'dev_mode' => true,
            'proxy_dir' => __DIR__ . '/../../var/doctrine/proxy',
            'cache_dir' => __DIR__ . '/../../var/doctrine/cache',
            'simple_annotation_reader' => false,
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'dbname' => getenv('DB_NAME'),
                'charset' => 'utf-8',
            ],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Auth/Entity'
            ],
            'types' => [
                \App\Auth\Entity\User\Types\IdType::NAME => \App\Auth\Entity\User\Types\IdType::class,
                \App\Auth\Entity\User\Types\RoleType::NAME => \App\Auth\Entity\User\Types\RoleType::class,
                \App\Auth\Entity\User\Types\StatusType::NAME => \App\Auth\Entity\User\Types\StatusType::class,
                \App\Auth\Entity\User\Types\EmailType::NAME => \App\Auth\Entity\User\Types\EmailType::class,
            ]
        ]
    ]
];