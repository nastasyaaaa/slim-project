<?php

namespace App\Auth\Entity\User;

use Ramsey\Uuid\Uuid;
use Webmozart\Assert\Assert;

class Id
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::uuid($value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public static function generate(): Id
    {
        return new static(Uuid::uuid4()->toString());
    }

    public function __toString()
    {
        return $this->getValue();
    }
}