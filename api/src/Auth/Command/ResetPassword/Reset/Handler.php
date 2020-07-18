<?php

namespace App\Auth\Command\ResetPassword\Reset;

use App\IFlusher;
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
        if (!($user = $this->users->findByPasswordResetToken($command->token))) {
            throw new \DomainException('User not found.');
        }
        
        $user->resetPassword(
            $command->token,
            new \DateTimeImmutable(),
            $this->hasher->hash($command->password)
        );

        $this->flusher->flush();
    }
}