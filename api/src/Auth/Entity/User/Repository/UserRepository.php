<?php

namespace App\Auth\Entity\User\Repository;

use DomainException;
use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\User;
use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Network;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository implements IUserRepository
{
    private string $entityClass = User::class;

    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($this->entityClass);
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repository->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function hasByNetwork(Network $network): bool
    {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.networks', 'n')
                ->where('n.name = :name and n.identity = :identity')
                ->setParameters([
                    ':name' => $network->getName(),
                    ':identity' => $network->getIdentity(),
                ])
                ->getQuery()
                ->getSingleScalarResult() > 0;
    }

    public function get(Id $id): User
    {
        $user = $this->repository->find($id->getValue());

        if (!$user) {
            throw new DomainException('User is not found.');
        }

        /** @var User $user */
        return $user;
    }

    public function getByEmail(Email $email): User
    {
        $user = $this->repository->findOneBy([
            'email' => $email->getValue()
        ]);

        if (!$user) {
            throw new DomainException('User is not found.');
        }

        /** @var User $user */
        return $user;
    }

    public function add(User $user)
    {
        $this->entityManager->persist($user);
    }

    public function findByConfirmToken(string $token): ?User
    {
        /** @var User|null $user */
        $user = $this->repository->findOneBy([
            'joinConfirmToken.value' => $token
        ]);

        return $user;
    }

    public function findByPasswordResetToken(string $token): ?User
    {
        /** @var User|null $user */
        $user = $this->repository->findOneBy([
            'passwordResetToken.value' => $token
        ]);

        return $user;
    }

    public function findNewEmailToken(string $token): ?User
    {
        /** @var User|null $user */
        $user = $this->repository->findOneBy([
            'newEmailToken.value' => $token
        ]);

        return $user;
    }

    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
    }
}