<?php

namespace Test\Functional;


class HomeTest extends WebTestCase
{
    public function testSuccess()
    {
        $response = $this->app()->handle(self::json('GET', '/'));

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals(json_encode('Hello, nana'), (string)$response->getBody());
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testMethod()
    {
        $response = $this->app()->handle(self::json('POST', '/'));

        self::assertEquals(405, $response->getStatusCode());
        self::assertEquals('Method Not Allowed', $response->getReasonPhrase());
    }
}