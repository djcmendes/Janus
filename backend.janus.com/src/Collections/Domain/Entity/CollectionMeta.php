<?php

declare(strict_types=1);

namespace App\Collections\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'janus_collections')]
#[ORM\UniqueConstraint(name: 'UNIQ_COLLECTION_NAME', columns: ['name'])]
class CollectionMeta
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** The actual DB table name — also used as the collection handle in routes */
    #[ORM\Column(length: 64, unique: true)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    /** Whether the collection is hidden from the navigation */
    #[ORM\Column]
    private bool $hidden = false;

    /** Whether the collection behaves as a singleton (one record) */
    #[ORM\Column]
    private bool $singleton = false;

    /** The field name used for manual sorting */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $sortField = null;

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

    public function getLabel(): ?string { return $this->label; }
    public function setLabel(?string $label): static { $this->label = $label; return $this->touch(); }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $icon): static { $this->icon = $icon; return $this->touch(); }

    public function getNote(): ?string { return $this->note; }
    public function setNote(?string $note): static { $this->note = $note; return $this->touch(); }

    public function isHidden(): bool { return $this->hidden; }
    public function setHidden(bool $hidden): static { $this->hidden = $hidden; return $this->touch(); }

    public function isSingleton(): bool { return $this->singleton; }
    public function setSingleton(bool $singleton): static { $this->singleton = $singleton; return $this->touch(); }

    public function getSortField(): ?string { return $this->sortField; }
    public function setSortField(?string $sortField): static { $this->sortField = $sortField; return $this->touch(); }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
