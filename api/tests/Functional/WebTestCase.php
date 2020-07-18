<?php

namespace Test\Functional;

use Slim\App;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

class WebTestCase extends TestCase
{
    private ?App $app = null;

    protected static function request(string $method, string $url): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $url);
    }

    protected static function json(string $method, string $url, array $body = []): ServerRequestInterface
    {
        $request = self::request($method, $url)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json');

        $request->getBody()->write(json_encode($body, JSON_THROW_ON_ERROR));

        return $request;
    }

    private function container(): ContainerInterface
    {
        return (require __DIR__ . '/../../config/container.php')();

    }

    protected function app(): App
    {
        if ($this->app === null) {
            $this->app = (require __DIR__ . '/../../config/app.php')($this->container());
        }

        return $this->app;
    }

}