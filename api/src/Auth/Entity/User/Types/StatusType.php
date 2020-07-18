<?php

namespace App\Auth\Entity\User\Types;

use App\Auth\Entity\User\Status;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class StatusType extends StringType
{
    public const NAME = 'auth_user_status';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        return $value instanceof Status ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Status
    {
        return $value instanceof Status ? $value : new Status($value);
    }

    public function getName(): string
    {
        return static::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}