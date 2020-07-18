<?php

namespace App\Auth\Test\Unit\Service;

use PHPUnit\Framework\TestCase;
use App\Auth\Service\Tokenizer;

/**
 * Class TokenizerTest
 * @package App\Auth\Test\Unit\Service
 * @covers Tokenizer
 */
class TokenizerTest extends TestCase
{
    public function testSuccess()
    {
        $interval = new \DateInterval('PT1H');
        $date = new \DateTimeImmutable('+1 day');

        $tokenizer = new Tokenizer($interval);

        $token = $tokenizer->generate($date);

        self::assertEquals($date->add($interval), $token->getExpires());
    }
}