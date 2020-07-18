<?php

namespace App\Auth\Command\ChangePassword;

use App\IFlusher;
use App\Auth\Entity\User\Id;
use App\Auth\Service\PasswordHasher;
use App\Auth\Entity\User\Repository\IUserRepository;

class Handler
{
    private IUserRepository $users;
    private IFlusher $flusher;
    private PasswordHasher $hasher;

    public function __construct(IUserRepository $users, IFlusher $flusher, PasswordHasher $hasher)
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->hasher = $hasher;
    }

    public function handle(Command $command)
    {
        $user = $this->users->get(new Id($command->id));

        $user->changePassword(
            $command->current,
            $command->new,
            $this->hasher
        );

        $this->flusher->flush();
    }
}