<?php

namespace App\Auth\Entity\User\Repository;

use DomainException;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Network;

interface IUserRepository
{
    public function hasByEmail(Email $email): bool;

    public function hasByNetwork(Network $network): bool;

    /**
     * @param Id $id
     * @return User
     * @throws DomainException
     */
    public function get(Id $id): User;

    /**
     * @param Email $email
     * @return User
     * @throws DomainException
     */
    public function getByEmail(Email $email): User;

    public function findByConfirmToken(string $token): ?User;

    public function findByPasswordResetToken(string $token): ?User;

    public function findNewEmailToken(string $token): ?User;

    public function add(User $user);

    public function remove(User $user): void;
}