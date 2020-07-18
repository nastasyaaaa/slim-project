<?php


namespace App\Auth\Test\Builder;


use Ramsey\Uuid\Uuid;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Role;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Entity\User\Network;

class UserBuilder
{
    private Id $id;
    private Email $email;
    private \DateTimeImmutable $date;
    private bool $active;
    private ?Network $network;
    private ?string $hash;
    private ?Token $token;

    public function __construct()
    {
        $this->id = Id::generate();
        $this->email = new Email('nastyaa1212@gmail.com');
        $this->date = new \DateTimeImmutable();
        $this->active = false;
    }

    public function active()
    {
        $clone = clone $this;
        $clone->active = true;
        return $clone;
    }

    public function withEmail(Email $email)
    {
        $clone = clone $this;
        $clone->email = $email;
        return $clone;
    }

    public function withJoinConfirmToken(Token $token)
    {
        $clone = clone $this;
        $clone->token = $token;
        return $clone;
    }

    public function buildByEmail(): User
    {
        $this->hash = 'hash';
        $this->token = $this->token ?? new Token(Uuid::uuid4(), new \DateTimeImmutable());

        $user = User::joinByEmail(
            $id = $this->id,
            $email = $this->email,
            $date = $this->date,
            $hash = $this->hash,
            $token = $this->token
        );

        if ($this->active) {
            $user->confirmJoin(
                $this->token->getValue(),
                $this->token->getExpires()->modify('-1 day'),
            );
        }

        return $user;
    }

    public function buildByNetwork(): User
    {
        $this->network = new Network('google', 'blbla-1');

        $user = User::joinByNetwork(
            $id = $this->id,
            $email = $this->email,
            $date = $this->date,
            $network = $this->network
        );

        return $user;
    }
}