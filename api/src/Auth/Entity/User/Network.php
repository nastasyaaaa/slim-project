<?php

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Network
{
    /**
     * @ORM\Column(type="string")
     */
    private string $name;
    /**
     * @ORM\Column(type="string")
     */
    private string $identity;


    public function __construct(string $name, string $identity)
    {
        Assert::notEmpty($name);
        Assert::notEmpty($identity);

        $this->name = mb_strtolower($name);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualTo(Network $identity): bool
    {
        return $identity->getName() === $this->getName()
            && $identity->getIdentity() === $this->getIdentity();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}