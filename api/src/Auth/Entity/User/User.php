<?php

namespace App\Auth\Entity\User;

use DomainException;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Auth\Service\PasswordHasher;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="auth_users")
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    /**
     * @ORM\Column(type="auth_user_id")
     * @ORM\Id
     */
    private Id $id;
    /**
     * @ORM\Column(type="auth_user_email", unique=true)
     */
    private Email $email;
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $date;
    /**
     * @ORM\Column(type="auth_user_status", length=16)
     */
    private Status $status;
    /**
     * @ORM\Column(type="auth_user_role", length=16)
     */
    private Role $role;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $hash = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $joinConfirmToken;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $passwordResetToken = null;
    /**
     * @ORM\Embedded(class="Token")
     */
    private ?Token $newEmailToken = null;
    /**
     * @ORM\Column(type="auth_user_email", nullable=true)
     */
    private ?Email $newEmail = null;

    /**
     * @var ArrayCollection|Collection
     * @ORM\OneToMany(targetEntity="UserNetwork", mappedBy="user", cascade={"all"}, orphanRemoval=true)
     */
    private Collection $networks;

    private function __construct(Id $id, Email $email, DateTimeImmutable $date, Status $status)
    {
        $this->id = $id;
        $this->email = $email;
        $this->date = $date;
        $this->status = $status;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function joinByEmail(Id $id, Email $email, DateTimeImmutable $date, string $hash, Token $token): User
    {
        $user = new self($id, $email, $date, Status::wait());

        $user->hash = $hash;
        $user->joinConfirmToken = $token;
        return $user;
    }

    public static function joinByNetwork(Id $id, Email $email, DateTimeImmutable $date, Network $networkIdentity): User
    {
        $user = new self($id, $email, $date, Status::active());

        $user->networks->add(new UserNetwork($user, $networkIdentity));
        return $user;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function getResetPasswordToken(): ?Token
    {
        return $this->passwordResetToken;
    }

    public function getNetworks(): ?array
    {
        return $this->networks
            ->map(fn(UserNetwork $userNetwork) => $userNetwork->getNetwork())
            ->toArray();
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function remove(): void
    {
        if (!$this->isWait()) {
            throw new DomainException('Can`t remove active user.');
        }
    }

    /**
     * @param string $token
     * @param DateTimeImmutable $date
     * @throws DomainException
     */
    public function confirmJoin(string $token, \DateTimeImmutable $date): void
    {
        if ($this->joinConfirmToken === null && $this->isActive()) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->joinConfirmToken->validate($token, $date);

        $this->status = Status::active();
        $this->joinConfirmToken = null;
    }

    /**
     * @param Network $identity
     * @throws DomainException
     */
    public function attachNetwork(Network $identity): void
    {
        if (!$this->isActive()) {
            throw new DomainException('Can`t attach network to inactive user.');
        }

        /** @var UserNetwork $existing */
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($identity)) {
                throw new DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new UserNetwork($this, $identity));
    }

    /**
     * @param Token $token
     * @param DateTimeImmutable $date
     * @throws DomainException
     */
    public function requestPasswordReset(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is inactive.');
        }

        if ($this->passwordResetToken !== null && !$this->passwordResetToken->isExpiredTo($date)) {
            throw new DomainException('Resetting already requested.');
        }

        $this->passwordResetToken = $token;
    }

    /**
     * @param string $token
     * @param DateTimeImmutable $date
     * @param string $newHash
     * @throws DomainException
     */
    public function resetPassword(string $token, DateTimeImmutable $date, string $newHash): void
    {
        if ($this->passwordResetToken === null) {
            throw new DomainException('Resetting is not requested.');
        }

        $this->passwordResetToken->validate($token, $date);
        $this->hash = $newHash;
        $this->passwordResetToken = null;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if ($this->hash === null) {
            throw new DomainException('User doesn`t have old password.');
        }

        if (!$hasher->validate($this->hash, $current)) {
            throw new DomainException('Old password is incorrect.');
        }

        $this->hash = $hasher->hash($new);
    }

    public function requestEmailChanging(Token $token, DateTimeImmutable $date, Email $email): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is inactive.');
        }

        if ($this->email->isEqualTo($email)) {
            throw new DomainException('Emails are the same.');
        }

        if ($this->newEmailToken !== null && !$this->newEmailToken->isExpiredTo($date)) {
            throw new DomainException('Change email already requested.');
        }

        $this->newEmailToken = $token;
        $this->newEmail = $email;
    }

    public function confirmChangeEmail(string $token, DateTimeImmutable $date): void
    {
        if ($this->newEmailToken === null || $this->newEmail === null) {
            throw new DomainException('Email change not requested.');
        }

        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqualTo($role)) {
            throw new DomainException('Roles are the same.');
        }

        $this->role = $role;
    }

    /**
     * @ORM\PostLoad
     */
    public function checkEmbeds(): void
    {
        if ($this->joinConfirmToken && $this->joinConfirmToken->isEmpty()) {
            $this->joinConfirmToken = null;
        }
        if ($this->passwordResetToken && $this->passwordResetToken->isEmpty()) {
            $this->passwordResetToken = null;
        }
        if ($this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->newEmailToken = null;
        }
    }
}