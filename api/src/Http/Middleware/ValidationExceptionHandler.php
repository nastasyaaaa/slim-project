<?php

namespace App\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use App\Http\Validator\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ValidationExceptionHandler implements MiddlewareInterface
{
    private ResponseFactoryInterface $responseFactory;

    /**
     * ValidationExceptionHandler constructor.
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $errors = [];

            /** @var ConstraintViolationInterface $violation */
            foreach($e->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            $response = ($this->responseFactory->createResponse(422))
                ->withHeader('Content-Type', 'application/json');

            $response->getBody()->write(
                json_encode([
                    'errors' => $errors
                ], JSON_THROW_ON_ERROR)
            );

            return $response;
        }
    }
}