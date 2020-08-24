<?php

namespace App\Http\Middleware;

use DomainException;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DomainExceptionHandlerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;
    private TranslatorInterface $translator;
    private ResponseFactoryInterface $responseFactory;

    /**
     * DomainExceptionHandlerMiddleware constructor.
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(LoggerInterface $logger, TranslatorInterface $translator, ResponseFactoryInterface $responseFactory)
    {
        $this->logger = $logger;
        $this->translator = $translator;
        $this->responseFactory = $responseFactory;
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

            $response = ($this->responseFactory->createResponse(409))
                ->withHeader('Content-Type', 'application/json');

            $response->getBody()->write(
                json_encode([
                    'message' => $this->translator->trans($exception->getMessage(), [], 'exceptions')
                ], JSON_THROW_ON_ERROR)
            );

            return $response;
        }
    }

}