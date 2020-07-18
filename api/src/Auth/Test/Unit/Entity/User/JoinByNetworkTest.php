<?php

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Role;
use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Network;

/**
 * Class JoinByNetworkTest
 * @package App\Auth\Test\Unit\Entity\User
 * @covers User::joinByNetwork
 */
class JoinByNetworkTest extends TestCase
{
    public function testSuccess()
    {
        $user = User::joinByNetwork(
            $id = Id::generate(),
            $email = new Email('nastyaa1212@gmail.com'),
            $date = new \DateTimeImmutable(),
            $network = new Network('google', 'sdskshfsdjkd'),
        );

        self::assertEquals($id, $user->getId());
        self::assertEquals($email, $user->getEmail());
        self::assertEquals($date, $user->getDate());

        self::assertCount(1, $user->getNetworks());
        self::assertEquals($network, $user->getNetworks()[0]);

        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());


        self::assertEquals(Role::USER, $user->getRole()->getName());
    }
}