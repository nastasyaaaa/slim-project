<?php

namespace App\Auth\Command\ResetPassword\Request;

use App\IFlusher;
use App\Auth\Entity\User\Email;
use App\Auth\Service\Tokenizer;
use App\Auth\Service\PasswordResetTokenSender;
use App\Auth\Entity\User\Repository\IUserRepository;

class Handler
{
    private IUserRepository $users;
    private IFlusher $flusher;
    private Tokenizer $tokenizer;
    private PasswordResetTokenSender $sender;

    public function __construct(
        IUserRepository $users,
        IFlusher $flusher,
        Tokenizer $tokenizer,
        PasswordResetTokenSender $sender
    )
    {
        $this->users = $users;
        $this->flusher = $flusher;
        $this->tokenizer = $tokenizer;
        $this->sender = $sender;
    }

    public function handle(Command $command)
    {
        $email = new Email($command->email);

        $user = $this->users->getByEmail($email);

        $date = new \DateTimeImmutable();

        $user->requestPasswordReset(
            $token = $this->tokenizer->generate($date),
            $date
        );

        $this->flusher->flush();
        $this->sender->send($email, $token);
    }
}