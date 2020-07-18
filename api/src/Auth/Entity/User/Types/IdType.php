<?php

namespace App\Auth\Entity\User\Types;

use App\Auth\Entity\User\Id;
use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class IdType extends GuidType
{
    public const NAME = 'auth_user_id';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Id ? $value->getValue() : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Id ? $value : new Id($value);
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