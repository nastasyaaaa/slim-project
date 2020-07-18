<?php

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Token
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $value;
    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expires;

    public function __construct(string $value, DateTimeImmutable $expires)
    {
        Assert::uuid($value);

        $this->value = $value;
        $this->expires = $expires;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function validate(string $token, \DateTimeImmutable $date): void
    {
        if ($this->isExpiredTo($date)) {
            throw new \DomainException('Token expired.');
        }

        if (!$this->isEqualTo($token)) {
            throw new \DomainException('Wrong token.');
        }
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires < $date;
    }

    public function isEqualTo(string $token): bool
    {
        return $this->value === $token;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }
}