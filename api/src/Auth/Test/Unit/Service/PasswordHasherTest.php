<?php

namespace App\Auth\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Auth\Service\PasswordHasher;

/**
 * Class PasswordHasherTest
 * @package App\Auth\Test\Unit\Service
 * @covers PasswordHasher
 */
class PasswordHasherTest extends TestCase
{
    private const MEMORY_COST = 16;

    public function testValidate()
    {
        $hasher = new PasswordHasher(self::MEMORY_COST);

        $hash = $hasher->hash($password = '123456');

        self::assertTrue($hasher->validate($password, $hash));
        self::assertFalse($hasher->validate('wrong', $hash));
    }

    public function testEmpty()
    {
        $hasher = new PasswordHasher(self::MEMORY_COST);

        $this->expectException(\InvalidArgumentException::class);
        $hasher->hash('');
    }

    public function testValidationEmpty()
    {
        $hasher = new PasswordHasher(self::MEMORY_COST);

        $this->expectException(\InvalidArgumentException::class);
        $hasher->validate('', 'asas');
    }
}