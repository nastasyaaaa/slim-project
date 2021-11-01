<?php

namespace App\Http\Test\Unit\Middleware;

use Test\Functional\WebTestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Translation\Translator;
use App\Http\Middleware\TranslationLocaleMiddleware;

class TranslationLocaleMiddlewareTest extends WebTestCase
{
    public function testDefault()
    {
        $translator = $this->createMock(Translator::class);
        $translator->expects(self::never())->method('setLocale');

        $middleware = new TranslationLocaleMiddleware($translator);

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($source = self::response());

        $response = $middleware->process(self::request('POST', 'http://test'), $handler);

        self::assertEquals($source, $response);
    }

    public function testSimpleAcceptLanguage()
    {
        $translator = $this->createMock(Translator::class);
        $translator
            ->expects(self::once())
            ->method('setLocale')
            ->with($this->equalTo('ru'));

        $middleware = new TranslationLocaleMiddleware($translator);

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(self::response());

        $request = self::request('POST', 'http://test')
            ->withHeader('Accept-Language', 'ru');

        $middleware->process($request, $handler);
    }
}