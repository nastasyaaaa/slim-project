<?php

namespace App\Auth\Test\Unit\Entity\User\ChangeEmail;

use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Email;
use App\Auth\Service\Tokenizer;
use App\Auth\Test\Builder\UserBuilder;

class ConfirmTest extends TestCase
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
            ->active()
            ->withEmail($old = new Email('old-email@gmail.com'))
            ->buildByEmail();

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging(
            $token = $this->tokenizer->generate($date),
            $date,
            $new = new Email('new-email@gmail.com')
        );

        self::assertNotNull($user->getNewEmailToken());
        self::assertNotNull($user->getNewEmail());

        $user->confirmChangeEmail($token->getValue(), $date->modify('+1 hour'));

        self::assertEquals($new, $user->getEmail());
        self::assertNotEquals($old, $user->getEmail());
        self::assertNull($user->getNewEmail());
        self::assertNull($user->getNewEmailToken());
    }

    public function testTokenExpired()
    {
        $user = (new UserBuilder())->active()->buildByEmail();

        $email = new Email('new-email@gmail.com');

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging(
            $token = $this->tokenizer->generate($date),
            $date,
            $email
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Token expired.');

        $user->confirmChangeEmail($token->getValue(), $date->modify('+3 days'));
    }

    public function testIncorrectToken()
    {
        $user = (new UserBuilder())->active()->buildByEmail();

        $email = new Email('new-email@gmail.com');

        $date = new \DateTimeImmutable();

        $user->requestEmailChanging(
            $token = $this->tokenizer->generate($date),
            $date,
            $email
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Wrong token.');

        $user->confirmChangeEmail($this->tokenizer->generate($date)->getValue(), $date);
    }

    public function testNotRequested()
    {
        $user = (new UserBuilder())->active()->buildByEmail();
        $date = new \DateTimeImmutable();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Email change not requested.');

        $user->confirmChangeEmail($this->tokenizer->generate($date)->getValue(), $date);
    }
}