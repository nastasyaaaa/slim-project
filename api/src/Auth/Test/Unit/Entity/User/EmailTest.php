<?php

namespace App\Auth\Test\Unit\Entity\User;

use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Email;

/**
 * Class EmailTest
 * @package App\Auth\Test\Unit\Entity\User
 * @covers Email
 */
class EmailTest extends TestCase
{
    public function testSuccess(): void
    {
        $value = 'nastyaa1212@gmail.com';
        $email = new Email($value);

        self::assertEquals($value, $email->getValue());
    }

    public function testCase(): void
    {
        $email = new Email('NastyaA1212@Gmail.Com');

        self::assertEquals('nastyaa1212@gmail.com', $email->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('NastyaA1212');
    }

    public function testEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Email('');
    }
}