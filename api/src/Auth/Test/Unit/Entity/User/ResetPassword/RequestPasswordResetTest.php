<?php

namespace App\Auth\Test\Unit\Entity\User\ResetPassword;

use PHPUnit\Framework\TestCase;
use App\Auth\Service\Tokenizer;
use App\Auth\Test\Builder\UserBuilder;

class RequestPasswordResetTest extends TestCase
{
    public function testSuccess()
    {
        $interval = new \DateInterval('PT1H');
        $tokenizer = new Tokenizer($interval);

        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $date = new \DateTimeImmutable();

        $user->requestPasswordReset(
            $token = $tokenizer->generate($date),
            $date
        );

        self::assertNotEmpty($user->getResetPasswordToken());
        self::assertEquals($user->getResetPasswordToken(), $token);
    }

    public function testAlreadyRequested()
    {
        $interval = new \DateInterval('P2D'); // ttl
        $tokenizer = new Tokenizer($interval);

        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $date = new \DateTimeImmutable();

        $user->requestPasswordReset(
            $token = $tokenizer->generate($date),
            $date
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Resetting already requested.');

        $user->requestPasswordReset(
            $token,
            $date->modify('+1 day')
        );
    }

    public function testReplaceOldToken()
    {
        $interval = new \DateInterval('P2D'); // ttl
        $tokenizer = new Tokenizer($interval);

        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $date = new \DateTimeImmutable();

        $user->requestPasswordReset(
            $oldToken = $tokenizer->generate($date),
            $date
        );

        self::assertEquals($user->getResetPasswordToken(), $oldToken);

        $newDate = $date->modify('+3 days');

        $user->requestPasswordReset(
            $newToken = $tokenizer->generate($newDate),
            $newDate
        );

        self::assertNotEquals($user->getResetPasswordToken(), $oldToken);
        self::assertEquals($user->getResetPasswordToken(), $newToken);
    }

    public function testRequestInactiveUser()
    {
        $interval = new \DateInterval('P2D'); // ttl
        $tokenizer = new Tokenizer($interval);

        $user = (new UserBuilder())
            ->buildByEmail();

        $date = new \DateTimeImmutable();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('User is inactive.');

        $user->requestPasswordReset(
            $tokenizer->generate($date),
            $date
        );

    }
}