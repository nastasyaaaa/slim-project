<?php

namespace App\Auth\Command\Remove;

use App\IFlusher;
use App\Auth\Entity\User\Id;
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

        $user->remove();
        $this->users->remove($user);

        $this->flusher->flush();
    }

}