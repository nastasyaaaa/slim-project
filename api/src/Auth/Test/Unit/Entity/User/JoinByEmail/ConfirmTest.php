<?php

namespace App\Auth\Test\Unit\Entity\User\JoinByEmail;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Role;
use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Token;
use App\Auth\Test\Builder\UserBuilder;
use App\Auth\Entity\User\User;

/**
 * Class ConfirmTest
 * @package App\Auth\Test\Unit\Entity\User\JoinByEmail
 * @covers User
 */
class ConfirmTest extends TestCase
{
    public function testSuccess()
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->buildByEmail();


        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());

        self::assertNull($user->getJoinConfirmToken());

        self::assertEquals(Role::USER, $user->getRole()->getName());
    }

    public function testWrong()
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->buildByEmail();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Wrong token.');

        $user->confirmJoin(
            Uuid::uuid4()->toString(),
            $token->getExpires()->modify('-1 day')
        );
    }

    public function testAlreadyActive()
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->active()
            ->buildByEmail();

        self::assertFalse($user->isWait());
        self::assertTrue($user->isActive());

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Confirmation is not required.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('-1 day')
        );

    }

    public function testTokenExpired()
    {
        $user = (new UserBuilder())
            ->withJoinConfirmToken($token = $this->createToken())
            ->buildByEmail();

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Token expired.');

        $user->confirmJoin(
            $token->getValue(),
            $token->getExpires()->modify('+2 days')
        );
    }

    private function createToken()
    {
        return new Token(
            Uuid::uuid4()->toString(),
            (new \DateTimeImmutable())->modify('+1 day')
        );
    }
}