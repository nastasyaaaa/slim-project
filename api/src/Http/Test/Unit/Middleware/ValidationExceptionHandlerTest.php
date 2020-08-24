<?php

namespace App\Http\Test\Unit\Middleware;

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use App\Http\Validator\ValidationException;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Http\Middleware\ValidationExceptionHandler;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationExceptionHandlerTest extends TestCase
{
    public function testNormal()
    {
        $middleware = new ValidationExceptionHandler();

        $source = (new ResponseFactory())->createResponse();

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($source);

        $response = $middleware->process(self::createRequest(), $handler);

        self::assertEquals($source, $response);
    }

    public function testException()
    {
        $middleware = new ValidationExceptionHandler();

        $violations = new ConstraintViolationList([
            new ConstraintViolation('Incorrect email', null, [], null, 'email', 'not-email'),
            new ConstraintViolation('Empty password', null, [], null, 'password', ''),
        ]);
        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willThrowException(new ValidationException($violations));

        $response = $middleware->process(self::createRequest(), $handler);

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        /** @var array $data */
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals([
            'errors' => [
                'email' => 'Incorrect email',
                'password' => 'Empty password',
            ]
        ], $data);
    }

    private static function createRequest(): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest('POST', 'http://test');
    }
}