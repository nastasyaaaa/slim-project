<?php

namespace App\Http\Actions;

use App\Http\Controllers\Controller;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Action extends Controller implements RequestHandlerInterface
{
    abstract public function handle(ServerRequestInterface $request): ResponseInterface;
}