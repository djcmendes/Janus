<?php

declare(strict_types=1);

namespace App\Relations\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Stores metadata about relationships between collections.
 * No FK constraints are added to user tables — this is purely declarative.
 *
 * M2O: many_collection.many_field → one_collection (junction_collection is null)
 * M2M: many_collection ↔ one_collection via junction_collection
 */
#[ORM\Entity]
#[ORM\Table(name: 'janus_relations')]
#[ORM\UniqueConstraint(name: 'UNIQ_RELATION_COLLECTION_FIELD', columns: ['many_collection', 'many_field'])]
class Relation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** The collection that holds the FK field (the "many" side) */
    #[ORM\Column(length: 64)]
    private string $manyCollection;

    /** The FK field name inside many_collection */
    #[ORM\Column(length: 64)]
    private string $manyField;

    /** The collection being pointed to (the "one" side) */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $oneCollection = null;

    /** The corresponding field on the one_collection side (for bi-directional O2M) */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $oneField = null;

    /** For M2M: the junction/pivot table name */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $junctionCollection = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(string $manyCollection, string $manyField)
    {
        $this->id              = Uuid::v7();
        $this->manyCollection  = $manyCollection;
        $this->manyField       = $manyField;
        $this->createdAt       = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getManyCollection(): string { return $this->manyCollection; }
    public function getManyField(): string { return $this->manyField; }

    public function getOneCollection(): ?string { return $this->oneCollection; }
    public function setOneCollection(?string $oneCollection): static { $this->oneCollection = $oneCollection; return $this->touch(); }

    public function getOneField(): ?string { return $this->oneField; }
    public function setOneField(?string $oneField): static { $this->oneField = $oneField; return $this->touch(); }

    public function getJunctionCollection(): ?string { return $this->junctionCollection; }
    public function setJunctionCollection(?string $junctionCollection): static { $this->junctionCollection = $junctionCollection; return $this->touch(); }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
