<?php

namespace App\Http\Middleware;

use DomainException;
use App\Http\JsonResponse;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class DomainExceptionHandlerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    /**
     * DomainExceptionHandlerMiddleware constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (DomainException $exception) {

            $this->logger->warning($exception->getMessage(), [
                'url' => $request->getUri(),
                'exception' => $exception
            ]);

            return new JsonResponse(['message' => $exception->getMessage()], 409);
        }
    }

}