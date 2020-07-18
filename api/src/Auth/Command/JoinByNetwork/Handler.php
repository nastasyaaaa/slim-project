<?php

namespace App\Auth\Command\JoinByNetwork;

use App\IFlusher;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
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
        $network = $command->networkIdentity;
        $email = $command->email;

        if ($this->users->hasByNetwork($network)) {
            throw new \DomainException('User with this network already exists.');
        }

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $user = User::joinByNetwork(
            Id::generate(),
            $email,
            new \DateTimeImmutable(),
            $network
        );

        $this->users->add($user);

        $this->flusher->flush();
    }
}