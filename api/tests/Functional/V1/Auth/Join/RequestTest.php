<?php

namespace Test\Functional\V1\Auth\Join;

use Test\Functional\WebTestCase;
use App\Auth\Fixture\UserFixture;

class RequestTest extends WebTestCase
{
    protected array $fixtures = [
        UserFixture::class
    ];

    public function testMethod()
    {
        $response = $this->app()->handle(self::json('PUT', '/v1/auth/join'));

        self::assertEquals(405, $response->getStatusCode());
    }

    public function testSuccess()
    {
        // clear mail messages
        $this->mailer()->clear();

        $email = 'user2@app.test';
        $password = '123456';

        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => $email,
            'password' => $password
        ]));

        self::assertEquals(201, $response->getStatusCode());
        self::assertTrue($this->mailer()->hasEmailSentTo('user2@app.test'));
    }

    public function testAlreadyExists()
    {
        $email = 'user@app.test';
        $password = '123456';

        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => $email,
            'password' => $password
        ]));

        self::assertEquals(409, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());
        self::assertArraySubset([
            'message' => 'User with this email already exists.'
        ], json_decode($body, true, 512,JSON_THROW_ON_ERROR));
    }

    public function testNotValidEmail()
    {
        $email = 'not-valid-email';
        $password = '123456';

        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => $email,
            'password' => $password
        ]));

        self::assertEquals(422, $response->getStatusCode());
        self::assertArraySubset([
            'errors' => [
                'email' => 'This value is not a valid email address.',

            ]
        ], json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function testEmpty()
    {
        $email = '';
        $password = '';

        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => $email,
            'password' => $password
        ]));

        self::assertEquals(422, $response->getStatusCode());
        self::assertArraySubset([
            'errors' => [
                'email' => 'This value should not be blank.',
                'password' => 'This value should not be blank.',

            ]
        ], json_decode((string) $response->getBody(), true, 512,JSON_THROW_ON_ERROR));
    }

    public function testTooShortPassword()
    {
        $email = 'user@app.test';
        $password = 'aa';

        $response = $this->app()->handle(self::json('POST', '/v1/auth/join', [
            'email' => $email,
            'password' => $password
        ]));

        self::assertEquals(422, $response->getStatusCode());
        self::assertArraySubset([
            'errors' => [
                'password' => 'This value is too short. It should have 6 characters or more.',

            ]
        ], json_decode((string) $response->getBody(), true, 512,JSON_THROW_ON_ERROR));
    }
}