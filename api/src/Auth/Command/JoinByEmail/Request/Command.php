<?php

namespace App\Auth\Command\JoinByEmail\Request;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public string $email = "";

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6, allowEmptyString=true)
     */
    public string $password = "";

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}