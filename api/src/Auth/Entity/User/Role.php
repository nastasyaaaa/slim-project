<?php

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

class Role
{
    public const USER = 'user';
    public const ADMIN = 'admin';
    public const MODERATOR = 'moderator';

    private static array $names = [
        self::USER,
        self::ADMIN,
        self::MODERATOR,
    ];

    protected string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, self::$names);

        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function user(): Role
    {
        return new self(self::USER);
    }

    public static function admin(): Role
    {
        return new self(self::ADMIN);
    }

    public static function moderator(): Role
    {
        return new self(self::MODERATOR);
    }

    public function isEqualTo(Role $role): bool
    {
        return $this->name === $role->name;
    }
}