<?php

namespace App\Auth\Test\Unit\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;

class RemoveUserTest extends TestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function testSuccess()
    {
        $user = (new UserBuilder())
            ->buildByEmail();

        $user->remove();
    }

    public function testActiveUser()
    {
        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Unable to remove active user.');

        $user->remove();
    }
}