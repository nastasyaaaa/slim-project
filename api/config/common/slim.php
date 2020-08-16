<?php


return [
  \Slim\Interfaces\CallableResolverInterface::class => static function(\Psr\Container\ContainerInterface $container) {
    return new \Slim\CallableResolver($container);
  }
];