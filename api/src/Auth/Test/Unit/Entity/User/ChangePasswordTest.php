<?php


namespace App\Auth\Test\Unit\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Service\PasswordHasher;
use App\Auth\Test\Builder\UserBuilder;

/**
 * Class ChangePasswordTest
 * @package App\Auth\Test\Unit\Entity\User
 * @covers \App\Auth\Entity\User\User::changePassword
 */
class ChangePasswordTest extends TestCase
{
    public function testSuccess()
    {
        $hasher = $this->createHasher(true, $hash = 'new-hash');

        $user = (new UserBuilder())->active()->buildByEmail();

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );

        self::assertEquals($hash, $user->getHash());
    }

    public function testIncorrectCurrentPassword()
    {
        $hasher = $this->createHasher(false, $hash = 'new-hash');

        $user = (new UserBuilder())->active()->buildByEmail();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Incorrect current password.');

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );
    }

    public function testByNetwork()
    {
        $hasher = $this->createHasher(true, $hash = 'new-hash');

        $user = (new UserBuilder())->active()->buildByNetwork();

        $this->expectExceptionMessage('User does not have an old password.');
        $this->expectException(\DomainException::class);

        $user->changePassword(
            'old-password',
            'new-password',
            $hasher
        );
    }

    private function createHasher($validate, $hash)
    {
        $hasher = $this->createStub(PasswordHasher::class);

        $hasher->method('validate')->willReturn($validate);
        $hasher->method('hash')->willReturn($hash);

        return $hasher;
    }
}