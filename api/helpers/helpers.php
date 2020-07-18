<?php

if (!function_exists('resolve')) {

    function resolve($abstract)
    {
        /** @var \Psr\Container\ContainerInterface $container */
        $container = (require __DIR__ . './../config/container.php')();

        return $container->get($abstract);
    }
}