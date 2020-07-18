<?php

namespace App\Auth\Service;

use Webmozart\Assert\Assert;

class PasswordHasher
{
    private int $memoryCost;

    public function __construct(int $memoryCost = PASSWORD_ARGON2_DEFAULT_MEMORY_COST)
    {
        $this->memoryCost = $memoryCost;
    }


    public function hash(string $password): string
    {
        Assert::notEmpty($password);
        $hash = password_hash($password, PASSWORD_ARGON2I, ['memory_cost' => $this->memoryCost]);

        if($hash === null) {
            throw new \RuntimeException('Invalid hash algo.');
        }
        if ($hash === false) {
            throw new \RuntimeException('Unable to generate hash.');
        }

        return $hash;
    }

    public function validate(string $password, string $hash): bool
    {
        Assert::notEmpty($password);
        return password_verify($password, $hash);
    }
}