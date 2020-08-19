<?php

namespace Functional;

use Test\Functional\WebTestCase;

class NotFoundTest extends WebTestCase
{
    public function testNotFound()
    {
        $response = $this->app()->handle(self::json('GET', '/not-found'));

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('Not Found', $response->getReasonPhrase());
    }
}