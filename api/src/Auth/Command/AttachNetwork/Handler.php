<?php

namespace App\Auth\Command\AttachNetwork;

use App\IFlusher;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Network;
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
        $network = new Network($command->network, $command->identity);

        if ($this->users->hasByNetwork($network)) {
            throw new \DomainException('User with this network already exists.');
        }

        $user = $this->users->get(new Id($command->id));

        $user->attachNetwork($network);

        $this->flusher->flush();
    }
}