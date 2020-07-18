<?php

namespace App\Auth\Command\ChangeEmail\Confirm;

use App\IFlusher;
use DomainException;
use DateTimeImmutable;
use App\Auth\Entity\User\Repository\IUserRepository;

class Handler
{
    private IUserRepository $users;
    private IFlusher $flusher;

    public function __construct(IUserRepository $users, IFlusher $flusher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        if (!($user = $this->users->findNewEmailToken($token = $command->token))) {
            throw new DomainException('Token not found.');
        }

        $user->confirmChangeEmail($token, new DateTimeImmutable());

        $this->flusher->flush();
    }
}