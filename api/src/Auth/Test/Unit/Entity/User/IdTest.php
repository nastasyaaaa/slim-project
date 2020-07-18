<?php

namespace App\Auth\Test\Unit\Entity\User;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Id;
use PHPUnit\Framework\TestCase;

/**
 * Class IdTest
 * @package App\Auth\Test\Unit\Entity\User
 * @covers Id
 */
class IdTest extends TestCase
{
    public function testSuccess(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $id = new Id($uuid);

        self::assertEquals($uuid, $id->getValue());
    }

    public function testRegister(): void
    {
        $uuid = Uuid::uuid4()->toString();
        $id = new Id(mb_strtoupper($uuid));

        self::assertNotEquals($uuid, $id->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Id('testId');
    }

    public function testGenerate(): void
    {
        $id = Id::generate();

        self::assertNotEmpty($id->getValue());
    }

    public function testEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Id('');
    }
}