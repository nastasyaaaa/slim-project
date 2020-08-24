<?php

if (!function_exists('resolve')) {

    function resolve($abstract)
    {
        /** @var \Psr\Container\ContainerInterface $container */
        $container = (require __DIR__ . './../config/container.php')();

        return $container->get($abstract);
    }
}

if (!function_exists('dd')) {

    function dd($smt)
    {
        var_dump($smt); die();
    }
}