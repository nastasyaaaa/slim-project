<?php

namespace App\Auth\Command\ChangeEmail\Request;

use App\IFlusher;
use DomainException;
use DateTimeImmutable;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Email;
use App\Auth\Service\Tokenizer;
use App\Auth\Service\INewEmailConfirmTokenSender;
use App\Auth\Entity\User\Repository\IUserRepository;

class Handler
{
    private IUserRepository $users;
    private Tokenizer $tokenizer;
    private IFlusher $flusher;
    private INewEmailConfirmTokenSender $sender;

    public function __construct(IUserRepository $users, Tokenizer $tokenizer, IFlusher $flusher, INewEmailConfirmTokenSender $sender)
    {
        $this->users = $users;
        $this->tokenizer = $tokenizer;
        $this->flusher = $flusher;
        $this->sender = $sender;
    }

    public function handle(Command $command)
    {
        $user = $this->users->get(new Id($command->id));
        $email = new Email($command->email);

        if ($this->users->hasByEmail($email)) {
            throw new DomainException('User with this email already exists.');
        }

        $date = new DateTimeImmutable();

        $user->requestEmailChanging(
            $token = $this->tokenizer->generate($date),
            $date,
            $email
        );

        $this->flusher->flush();

        $this->sender->send($email, $token);
    }

}