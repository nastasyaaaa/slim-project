<?php

namespace App\Auth\Command\ChangeRole;

use App\IFlusher;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
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
        $user = $this->users->get(new Id($command->id));

        $user->changeRole(
            new Role($command->role)
        );

        $this->flusher->flush();
    }
}