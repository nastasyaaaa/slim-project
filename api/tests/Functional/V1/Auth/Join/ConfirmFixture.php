<?php

namespace Test\Functional\V1\Auth\Join;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class ConfirmFixture extends AbstractFixture
{
    public const VALID = '3c4dd497-1798-41b4-96ed-fe2b42f342cf';
    public const EXPIRED = 'd78c9d34-c936-4d88-a088-6ca078d2d769';

    public function load(ObjectManager $manager)
    {
        // valid
        $user = User::joinByEmail(
            Id::generate(),
            new Email('valid@app.test'),
            $date = new \DateTimeImmutable(),
            'password-hash',
            new Token($value = self::VALID, $date->modify('+1 hour'))
        );

        $manager->persist($user);

        // expired
        $user = User::joinByEmail(
            Id::generate(),
            new Email('expired@app.test'),
            $date = new \DateTimeImmutable(),
            'password-hash',
            new Token($value = self::EXPIRED, $date->modify('-2 hours'))
        );

        $manager->persist($user);

        $manager->flush();
    }
}