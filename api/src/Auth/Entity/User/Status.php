<?php


namespace App\Auth\Entity\User;


use Webmozart\Assert\Assert;

class Status
{
    private const WAIT = 'wait';
    private const ACTIVE = 'active';

    private string $status;

    public static $statuses = [
        self::WAIT,
        self::ACTIVE,
    ];

    public function __construct(string $status)
    {
        Assert::oneOf($status, self::$statuses);
        $this->status = $status;
    }

    public static function wait(): Status
    {
        return new self(self::WAIT);
    }

    public static function active(): Status
    {
        return new self(self::ACTIVE);
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function isWait(): bool
    {
        return $this->status === self::WAIT;
    }
}