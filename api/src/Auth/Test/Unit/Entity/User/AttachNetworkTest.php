<?php

namespace App\Auth\Test\Unit\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;
use App\Auth\Entity\User\Network;

class AttachNetworkTest extends TestCase
{
    public function testJoinedByEmailSuccess()
    {
        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $networkIdentity = new Network($network = 'telegram', $identity = 'some-identity-string');

        $user->attachNetwork($networkIdentity);

        self::assertCount(1, $user->getNetworks());
        self::assertEquals($networkIdentity, $user->getNetworks()[0] ?? null);
    }

    public function testInactiveUser()
    {
        $user = (new UserBuilder())
            ->buildByEmail();

        $networkIdentity = new Network($network = 'telegram', $identity = 'some-identity-string');

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Can`t attach network to inactive user.');

        $user->attachNetwork($networkIdentity);
    }

    public function testJoinedByNetwork()
    {
        $user = (new UserBuilder())
            ->buildByNetwork();

        $networkIdentity = new Network($network = 'telegram', $identity = 'some-identity-string');

        $user->attachNetwork($networkIdentity);

        self::assertCount(2, $user->getNetworks());
        self::assertEquals($networkIdentity, $user->getNetworks()[1] ?? null);
    }

    public function testAlreadyAttached()
    {
        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $networkIdentity = new Network($network = 'telegram', $identity = 'some-identity-string');

        $user->attachNetwork($networkIdentity);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Network is already attached.');

        $user->attachNetwork($networkIdentity);
    }
}