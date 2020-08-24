<?php

namespace App\Auth\Test\Unit\Entity\User\ChangeEmail;

use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Email;
use App\Auth\Service\Tokenizer;
use App\Auth\Test\Builder\UserBuilder;

/**
 * Class RequestTest
 * @package App\Auth\Test\Unit\Entity\User\ChangeEmail
 * @covers \App\Auth\Entity\User\User::requestEmailChanging
 */
class RequestTest extends TestCase
{
    private Tokenizer $tokenizer;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tokenizer = new Tokenizer(new \DateInterval('P1D'));
    }

    public function testSuccess()
    {
        $user = (new UserBuilder())
            ->withEmail($old = new Email('old-email@gmail.com'))
            ->active()
            ->buildByEmail();

        $new = new Email('new-email@gmail.com');

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging(
            $token = $this->tokenizer->generate($date),
            $date,
            $new
        );

        self::assertEquals($new, $user->getNewEmail());
        self::assertEquals($old, $user->getEmail());
        self::assertEquals($token, $user->getNewEmailToken());
    }

    public function testAlreadyRequested()
    {
        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $email = new Email('new-email@gmail.com');

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging(
            $this->tokenizer->generate($date),
            $date,
            $email
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Changing is already requested.');

        $user->requestEmailChanging(
            $this->tokenizer->generate($date),
            $date,
            $email
        );
    }

    public function testInactiveUser()
    {
        $user = (new UserBuilder())->buildByEmail();

        $email = new Email('new-email@gmail.com');

        $date = new \DateTimeImmutable();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('User is not active.');

        $user->requestEmailChanging(
            $this->tokenizer->generate($date),
            $date,
            $email
        );
    }

    public function testSameEmail()
    {
        $user = (new UserBuilder())
            ->withEmail($email = new Email('email@gmail.com'))
            ->active()
            ->buildByEmail();

        $date = new \DateTimeImmutable();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email is already same.');

        $user->requestEmailChanging(
            $this->tokenizer->generate($date),
            $date,
            $email
        );
    }
}