<?php

declare(strict_types=1);

namespace App\Roles\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'roles')]
#[ORM\UniqueConstraint(name: 'UNIQ_ROLE_NAME', columns: ['name'])]
class Role
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    /** Require 2FA for all users in this role */
    #[ORM\Column]
    private bool $enforceTfa = false;

    /** Grants full administrative access */
    #[ORM\Column]
    private bool $adminAccess = false;

    /** Allows users in this role to access the application */
    #[ORM\Column]
    private bool $appAccess = true;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(string $name)
    {
        $this->id        = Uuid::v7();
        $this->name      = $name;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this->touch(); }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this->touch(); }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $icon): static { $this->icon = $icon; return $this->touch(); }

    public function isEnforceTfa(): bool { return $this->enforceTfa; }
    public function setEnforceTfa(bool $enforceTfa): static { $this->enforceTfa = $enforceTfa; return $this->touch(); }

    public function isAdminAccess(): bool { return $this->adminAccess; }
    public function setAdminAccess(bool $adminAccess): static { $this->adminAccess = $adminAccess; return $this->touch(); }

    public function isAppAccess(): bool { return $this->appAccess; }
    public function setAppAccess(bool $appAccess): static { $this->appAccess = $appAccess; return $this->touch(); }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
