<?php

namespace Test\Functional;

use Slim\App;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\EntityManagerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class WebTestCase extends TestCase
{
    private ?App $app = null;

    protected array $fixtures = [];


    protected function setUp(): void
    {
        $this->loadFixtures();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->app = null;
        parent::tearDown();
    }

    private function loadFixtures()
    {
        $container = $this->container();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        $executor = new ORMExecutor($em, new ORMPurger($em));

        $loader = new Loader();

        foreach($this->fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $executor->execute($loader->getFixtures());
    }

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