<?php

namespace App\Http\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

abstract class Controller
{
    protected ResponseFactoryInterface $factory;
    protected ContainerInterface $container;

    public function __construct(ResponseFactoryInterface $factory, ContainerInterface $container)
    {
        $this->factory = $factory;
        $this->container = $container;
    }

    protected function response($data, int $code = 200, $phrase = "")
    {
        $response = $this->factory->createResponse($code, $phrase);
        $response->getBody()->write($data);

        return $response;
    }

    protected function jsonResponse($data, int $code = 200, $phrase = "")
    {
        $response = $this->factory->createResponse($code, $phrase);
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR, 512));

        return $response->withHeader('Content-Type', 'application/json');
    }
}