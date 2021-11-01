<?php

namespace Test\Functional;

use Slim\App;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\ORM\EntityManagerInterface;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

class WebTestCase extends TestCase
{
    use ArraySubsetAsserts;

    private ?App $app = null;
    private ?MailerClient $mailer = null;

    protected array $fixtures = [];

    protected function setUp(): void
    {
        $this->loadFixtures();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        // purge DB after each test
        $container = $this->container();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        (new ORMPurger($em))->purge();
        //

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

        foreach($this->fixtures as $fixtureClass) {
            /** @var FixtureInterface $fixture */
            $fixture = $container->get($fixtureClass);

            $loader->addFixture($fixture);
        }

        $executor->execute($loader->getFixtures());
    }

    protected static function json(string $method, string $path, array $body = []): ServerRequestInterface
    {
        $request = self::request($method, $path)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write(json_encode($body, JSON_THROW_ON_ERROR));
        return $request;
    }

    protected static function request(string $method, string $path): ServerRequestInterface
    {
        return (new ServerRequestFactory())->createServerRequest($method, $path);
    }

    protected static function response(int $code = StatusCodeInterface::STATUS_OK): ResponseInterface
    {
        return (new ResponseFactory())->createResponse($code);
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


    protected function mailer(): MailerClient
    {
        if ($this->mailer === null) {
            $this->mailer = new MailerClient();
        }

        return $this->mailer;
    }

}