<?php

declare(strict_types=1);

namespace App\Users\Domain\Entity;

use App\Roles\Domain\Entity\Role;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_EMAIL', columns: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private string $password = '';

    #[ORM\Column(length: 20)]
    private string $status = 'active'; // active | invited | suspended

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastAccessAt = null;

    #[ORM\Column(nullable: true)]
    private ?string $inviteToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $inviteTokenExpiresAt = null;

    #[ORM\ManyToOne(targetEntity: Role::class)]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Role $role = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(string $email)
    {
        $this->id        = Uuid::v7();
        $this->email     = $email;
        $this->createdAt = new \DateTimeImmutable();
    }

    // ── Getters & setters ──────────────────────────────────────────────────

    public function getId(): ?Uuid { return $this->id; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this->touch(); }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this->touch(); }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $n): static { $this->firstName = $n; return $this->touch(); }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $n): static { $this->lastName = $n; return $this->touch(); }

    public function getLastAccessAt(): ?\DateTimeImmutable { return $this->lastAccessAt; }
    public function touchLastAccess(): static { $this->lastAccessAt = new \DateTimeImmutable(); return $this; }

    public function getInviteToken(): ?string { return $this->inviteToken; }
    public function getInviteTokenExpiresAt(): ?\DateTimeImmutable { return $this->inviteTokenExpiresAt; }

    public function setInviteToken(string $token, \DateTimeImmutable $expiresAt): static
    {
        $this->inviteToken           = $token;
        $this->inviteTokenExpiresAt  = $expiresAt;
        return $this->touch();
    }

    public function clearInviteToken(): static
    {
        $this->inviteToken          = null;
        $this->inviteTokenExpiresAt = null;
        return $this->touch();
    }

    public function getRole(): ?Role { return $this->role; }
    public function setRole(?Role $role): static { $this->role = $role; return $this->touch(); }

    public function isInviteTokenValid(): bool
    {
        return $this->inviteToken !== null
            && $this->inviteTokenExpiresAt !== null
            && $this->inviteTokenExpiresAt > new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }
    public function softDelete(): static { $this->deletedAt = new \DateTimeImmutable(); return $this; }

    // ── UserInterface ──────────────────────────────────────────────────────

    public function getUserIdentifier(): string { return $this->email; }

    public function getRoles(): array
    {
        $roles   = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this->touch(); }

    // ── PasswordAuthenticatedUserInterface ─────────────────────────────────

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function eraseCredentials(): void {}

    // ── Private helpers ────────────────────────────────────────────────────

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
