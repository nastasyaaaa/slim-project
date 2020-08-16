<?php

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Email
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);

        /** @var string $value */
        $value = mb_strtolower($value);

        Assert::email($value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqualTo(Email $email): bool
    {
        return $this->value === $email->value;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}