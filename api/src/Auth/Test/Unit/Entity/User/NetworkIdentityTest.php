<?php

namespace App\Auth\Test\Unit\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Network;

/**
 * Class NetworkIdentityTest
 * @package App\Auth\Test\Unit\Entity\User
 * @covers Network
 */
class NetworkIdentityTest extends TestCase
{
    public function testSuccess()
    {
        $identity = new Network('google', 'gfdfghj6545678');

        self::assertNotEmpty($identity->getName());
        self::assertNotEmpty($identity->getIdentity());
    }

    public function testEmptyNetwork()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Network('', 'gfdfghj6545678');
    }

    public function testEmptyIdentity()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Network('google', '');
    }

    public function testEqualsTo()
    {
        $fIdentity = new Network('google', 'blabla');
        $sIdentity = new Network('google', 'blabla');

        self::assertTrue($fIdentity->isEqualTo($sIdentity));
        self::assertTrue($sIdentity->isEqualTo($fIdentity));
    }

    public function testNotEqualsTo()
    {
        $fIdentity = new Network('google-1', 'blabla');
        $sIdentity = new Network('google', 'blabla');

        self::assertFalse($fIdentity->isEqualTo($sIdentity));
        self::assertFalse($sIdentity->isEqualTo($fIdentity));
    }
}