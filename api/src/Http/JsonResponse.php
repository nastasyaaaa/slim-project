<?php

namespace App\Http;

use Slim\Psr7\Headers;
use Slim\Psr7\Response;
use Slim\Psr7\Factory\StreamFactory;
use Fig\Http\Message\StatusCodeInterface;

class JsonResponse extends Response
{
    public function __construct($body, int $status = StatusCodeInterface::STATUS_OK)
    {
        parent::__construct(
            $status,
            new Headers([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]),
            (new StreamFactory)->createStream(json_encode($body, JSON_THROW_ON_ERROR, 512))
        );
    }
}