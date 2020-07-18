<?php

namespace App\Auth\Service;

use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Token;

class Tokenizer
{
    protected \DateInterval $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(\DateTimeImmutable $date): Token
    {
        $expires = $date->add($this->interval);

        return new Token(Uuid::uuid4(), $expires);
    }
}