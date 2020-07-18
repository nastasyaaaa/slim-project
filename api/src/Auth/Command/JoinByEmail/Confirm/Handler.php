<?php

namespace App\Auth\Command\JoinByEmail\Confirm;

use App\IFlusher;
use App\Auth\Entity\User\Repository\IUserRepository;

class Handler
{
    private IUserRepository $userRepository;
    private IFlusher $flusher;

    public function __construct(IUserRepository $userRepository, IFlusher $flusher)
    {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        if (!$user = $this->userRepository->findByConfirmToken($command->token)) {
            throw new \DomainException('User not found');
        }

        $user->confirmJoin($command->token, new \DateTimeImmutable());

        $this->flusher->flush();
    }
}