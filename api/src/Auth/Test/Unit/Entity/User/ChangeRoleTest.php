<?php

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Role;
use PHPUnit\Framework\TestCase;
use App\Auth\Test\Builder\UserBuilder;

class ChangeRoleTest extends TestCase
{
    public function testSuccess()
    {
        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $user->changeRole(
            $role = Role::moderator()
        );

        self::assertEquals($role, $user->getRole());
    }

    public function testSameRole()
    {
        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Roles are the same.');

        $user->changeRole(
            Role::user()
        );
    }

    public function testInvalidRole()
    {
        $user = (new UserBuilder())
            ->active()
            ->buildByEmail();

        $this->expectException(\InvalidArgumentException::class);

        $user->changeRole(
            new Role('incorrect')
        );
    }
}