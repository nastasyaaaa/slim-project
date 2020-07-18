<?php

namespace App\Auth\Test\Unit\Entity\User\JoinByEmail;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;

/**
 * Class RequestTest
 * @package App\Auth\Test\Unit\Entity\User\JoinByEmail
 * @covers \App\Auth\Entity\User\User
 */
class RequestTest extends TestCase
{
    public function testSuccess()
    {
        $user = User::joinByEmail(
            $id = Id::generate(),
            $email = new Email('nastyaa1212@gmail.com'),
            $date = new \DateTimeImmutable(),
            $hash = 'hash',
            $token = new Token(Uuid::uuid4(), new \DateTimeImmutable())
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($hash, $user->getHash());
        self::assertEquals($token, $user->getJoinConfirmToken());

        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }

    public function testFailure()
    {
        $user = User::joinByEmail(
            $id = Id::generate(),
            $email = new Email('nastyaa1212@gmail.com'),
            $date = new \DateTimeImmutable(),
            $hash = 'hash',
            $token = new Token(Uuid::uuid4(), new \DateTimeImmutable())
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals(new Email('nastyaa1212@gmail.com'), $user->getEmail());
        self::assertEquals($date, $user->getDate());
        self::assertEquals($hash, $user->getHash());
        self::assertEquals($token, $user->getJoinConfirmToken());
    }
}