<?php

namespace App\Auth\Command\JoinByNetwork;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Network;

class Command
{
    public Network $networkIdentity;
    public Email $email;

    public function __construct(Network $networkIdentity, Email $email)
    {
        $this->networkIdentity = $networkIdentity;
        $this->email = $email;
    }
}