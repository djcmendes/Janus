<?php

declare(strict_types=1);

namespace App\Dashboards\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'dashboards')]
class Dashboard
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $icon;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note;

    /** Owner — null means it is a shared/global dashboard */
    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $userId;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        string  $name,
        ?string $icon   = null,
        ?string $note   = null,
        ?string $userId = null,
    ) {
        $this->id        = Uuid::v7()->toRfc4122();
        $this->name      = $name;
        $this->icon      = $icon;
        $this->note      = $note;
        $this->userId    = $userId;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getIcon(): ?string { return $this->icon; }
    public function getNote(): ?string { return $this->note; }
    public function getUserId(): ?string { return $this->userId; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function setName(string $name): static
    {
        $this->name      = $name;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon      = $icon;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function setNote(?string $note): static
    {
        $this->note      = $note;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function isOwnedBy(string $userId): bool
    {
        return $this->userId === $userId;
    }
}
