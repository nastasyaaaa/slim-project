<?php

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return static function (): ContainerInterface {
    $builder = new ContainerBuilder();

    $dependencies = require __DIR__ . '/dependencies.php';

    $builder->addDefinitions($dependencies);

    return $builder->build();
};