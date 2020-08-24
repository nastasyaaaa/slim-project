<?php

namespace Test\Functional\V1\Auth\Join;

use App\Auth\Entity\User\Id;
use Test\Functional\WebTestCase;

class ConfirmTest extends WebTestCase
{
    protected array $fixtures = [
        ConfirmFixture::class
    ];

    public function testMethod()
    {
        $response = $this->app()->handle(self::json('GET', '/v1/auth/confirm', [
            'token' => ConfirmFixture::VALID
        ]));

        self::assertEquals(405, $response->getStatusCode());
    }

    public function testValid()
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/confirm', [
            'token' => ConfirmFixture::VALID
        ]));

        self::assertEquals(200, $response->getStatusCode());
    }

    public function testExpired()
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/confirm', [
            'token' => ConfirmFixture::EXPIRED
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertArraySubset([
            'message' => 'Token is expired.'
        ], json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    public function testExpiredRU()
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/confirm', [
            'token' => ConfirmFixture::EXPIRED
        ])->withHeader('Accept-Language', 'ru'));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertArraySubset([
            'message' => 'Время действия токена истекло.'
        ], json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    public function testUserNotFound()
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/confirm', [
            'token' => (string)Id::generate()
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertArraySubset([
            'message' => 'User is not found.'
        ], json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    public function testUserNotFoundRU()
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/confirm', [
            'token' => (string)Id::generate()
        ])->withHeader('Accept-Language', 'ru'));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertArraySubset([
            'message' => 'Пользователь не найден.'
        ], json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    public function testNotValidTokenUUID()
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/confirm', [
            'token' => 'token-uuid'
        ]));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertArraySubset([
            'errors' => [
                'token' => 'This is not a valid UUID.'
            ]
        ], json_decode($body, true, 512, JSON_THROW_ON_ERROR));
    }

    public function testEmptyToken()
    {
        $response = $this->app()->handle(self::json('POST', '/v1/auth/confirm', [
            'token' => ''
        ]));

        self::assertEquals(422, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertArraySubset([
            'errors' => [
                'token' => 'This value should not be blank.'
            ]
        ], json_decode($body, true, 512,JSON_THROW_ON_ERROR));
    }
}