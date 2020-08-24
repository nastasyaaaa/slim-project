<?php

namespace App\Auth\Test\Unit\Entity\User\ResetPassword;

use App\Auth\Entity\User\User;
use PHPUnit\Framework\TestCase;
use App\Auth\Service\Tokenizer;
use App\Auth\Service\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;

class PasswordResetTest extends TestCase
{
    private User $user;
    private PasswordHasher $hasher;
    private Tokenizer $tokenizer;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $this->tokenizer = new Tokenizer(new \DateInterval('P1D'));
        $this->hasher = new PasswordHasher(16);
    }

    public function testSuccess()
    {
        $date = new \DateTimeImmutable();

        $this->user->requestPasswordReset(
            $token = $this->tokenizer->generate($date),
            $date
        );

        $this->user->resetPassword(
            $token->getValue(),
            $date->modify('+2 hours'),
            $hash = $this->hasher->hash('123456')
        );

        self::assertNull($this->user->getResetPasswordToken());
        self::assertEquals($this->user->getHash(), $hash);
    }

    public function testNotRequested()
    {
        $date = new \DateTimeImmutable();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Resetting is not requested.');

        $this->user->resetPassword(
            $this->tokenizer->generate($date)->getValue(),
            $date->modify('+2 hours'),
            $hash = $this->hasher->hash('123456')
        );
    }

    public function testInvalidToken()
    {
        $date = new \DateTimeImmutable();

        $this->user->requestPasswordReset(
            $token = $this->tokenizer->generate($date),
            $date
        );

        $invalidToken = $this->tokenizer->generate($date);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Wrong token.');

        $this->user->resetPassword(
            $invalidToken->getValue(),
            $date->modify('+2 hours'),
            $hash = $this->hasher->hash('123456')
        );
    }

    public function testTokenExpired()
    {
        $date = new \DateTimeImmutable();

        $this->user->requestPasswordReset(
            $token = $this->tokenizer->generate($date),
            $date
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Token is expired.');

        $this->user->resetPassword(
            $token->getValue(),
            $date->modify('+3 days'),
            $hash = $this->hasher->hash('123456')
        );
    }
}