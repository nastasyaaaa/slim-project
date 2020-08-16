<?php

namespace App\Auth\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;

interface IJoinConfirmationSender
{
    public function send(Email $email, Token $token): void;
}