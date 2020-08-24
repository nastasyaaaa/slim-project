<?php

namespace App\Http\Controllers;

use App\Http\Validator\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

abstract class Controller
{
    protected ResponseFactoryInterface $factory;
    protected ContainerInterface $container;
    protected Validator $validator;

    public function __construct(ResponseFactoryInterface $factory,
                                ContainerInterface $container,
                                Validator $validator)
    {
        $this->factory = $factory;
        $this->container = $container;
        $this->validator = $validator;
    }

    protected function response($data, int $code = 200, $phrase = "")
    {
        $response = $this->factory->createResponse($code, $phrase);
        $response->getBody()->write($data);

        return $response;
    }

    protected function jsonResponse($data, int $code = 200, $phrase = "")
    {
        $response = $this->response(json_encode($data, JSON_THROW_ON_ERROR, 512), $code, $phrase);

        return $response->withHeader('Content-Type', 'application/json');
    }
}