<?php

namespace App\Auth\Command\JoinByEmail\Confirm;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid()
     */
    public string $token = '';

    /**
     * Command constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }


}