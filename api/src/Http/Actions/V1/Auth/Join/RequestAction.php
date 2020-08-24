<?php

namespace App\Http\Actions\V1\Auth\Join;

use App\Http\EmptyResponse;
use App\Http\Actions\Action;
use App\Http\Validator\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use App\Auth\Command\JoinByEmail\Request\Handler;
use App\Auth\Command\JoinByEmail\Request\Command;

class RequestAction extends Action
{
    private Handler $handler;

    public function __construct(ResponseFactoryInterface $factory,
                                ContainerInterface $container,
                                Validator $validator,
                                Handler $handler)
    {
        parent::__construct($factory, $container, $validator);
        $this->handler = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();

        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        $command = new Command($email, $password);

        $this->validator->validate($command);

        $this->handler->handle($command);
        return new EmptyResponse(201);
    }
}