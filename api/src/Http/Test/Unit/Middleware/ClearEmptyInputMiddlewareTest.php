<?php

namespace App\Http\Test\Unit\Middleware;

use Test\Functional\WebTestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Http\Middleware\ClearEmptyInputMiddleware;

class ClearEmptyInputMiddlewareTest extends WebTestCase
{
    public function testSuccess()
    {
        $middleware = new ClearEmptyInputMiddleware();

        $request = self::request('GET', 'http://test')
            ->withParsedBody([
                'null' => null,
                'test' => '             sdsdsdsd  ',
                'string' => 'String ',
                'nested' => [
                    'null' => null,
                    'space' => ' '
                ]
            ]);

        $handler = $this->createMock(RequestHandlerInterface::class);

        $handler->expects(self::once())->method('handle')
            ->willReturnCallback(static function (ServerRequestInterface $request): ResponseInterface {
                self::assertEquals([
                    'null' => null,
                    'test' => 'sdsdsdsd',
                    'string' => 'String',
                    'nested' => [
                        'null' => null,
                        'space' => ''
                    ]
                ], $request->getParsedBody());

                return (new ResponseFactory())->createResponse();
            });

        $middleware->process($request, $handler);
    }
}