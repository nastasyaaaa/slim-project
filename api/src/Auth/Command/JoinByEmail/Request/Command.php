<?php


namespace App\Auth\Command\JoinByEmail\Request;


use App\Auth\Entity\User\Email;

class Command
{
    public Email $email;
    public string $password = "";

    public function __construct(Email $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }
}