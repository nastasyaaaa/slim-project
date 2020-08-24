<?php

namespace App\Auth\Command\JoinByEmail\Request;

use App\IFlusher;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Service\Tokenizer;
use App\Auth\Entity\User\Email;
use App\Auth\Service\PasswordHasher;
use App\Auth\Service\IJoinConfirmationSender;
use App\Auth\Entity\User\Repository\IUserRepository;

class Handler
{
    private IUserRepository $users;
    private PasswordHasher $hasher;
    private Tokenizer $tokenizer;
    private IFlusher $flusher;
    private IJoinConfirmationSender $sender;


    public function __construct(IUserRepository $users,
                                PasswordHasher $hasher,
                                Tokenizer $tokenizer,
                                IFlusher $flusher,
                                IJoinConfirmationSender $sender)
    {
        $this->users = $users;
        $this->hasher = $hasher;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }


    public function handle(Command $command): void
    {
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new \DomainException('User with this email already exists.');
        }

        $date = new \DateTimeImmutable();

        $user = User::joinByEmail(
            Id::generate(),
            $email,
            $date,
            $this->hasher->hash($command->password),
            $token = $this->tokenizer->generate($date)
        );

        $this->users->add($user);

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }
}