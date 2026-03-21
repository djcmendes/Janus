<?php

declare(strict_types=1);

namespace App\Versions\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * A named snapshot of a content item at a point in time.
 *
 * - `collection` + `item` + `key` must be unique: only one version named "draft"
 *   per item at a time.
 * - `data` is immutable once saved; use key/delta for mutable metadata.
 */
#[ORM\Entity]
#[ORM\Table(name: 'versions')]
#[ORM\UniqueConstraint(name: 'uniq_version_collection_item_key', columns: ['collection', 'item', 'version_key'])]
#[ORM\Index(name: 'idx_version_collection_item', columns: ['collection', 'item'])]
class Version
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    /** The collection (table) the versioned item belongs to */
    #[ORM\Column(length: 64)]
    private string $collection;

    /** The item's UUID/ID within the collection */
    #[ORM\Column(length: 36)]
    private string $item;

    /** Human-readable version name, e.g. "main", "draft", "v1.0" */
    #[ORM\Column(name: 'version_key', length: 64)]
    private string $key;

    /** Full item data snapshot (JSON) */
    #[ORM\Column(type: 'json')]
    private array $data;

    /** Diff vs. previous version (JSON, optional) */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $delta = null;

    /** UUID of the user who created this version */
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $userId = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct(
        string  $collection,
        string  $item,
        string  $key,
        array   $data,
        ?array  $delta  = null,
        ?string $userId = null,
    ) {
        $this->id         = Uuid::v7();
        $this->collection = $collection;
        $this->item       = $item;
        $this->key        = $key;
        $this->data       = $data;
        $this->delta      = $delta;
        $this->userId     = $userId;
        $this->createdAt  = new \DateTimeImmutable();
    }

    public function getId(): ?Uuid { return $this->id; }
    public function getCollection(): string { return $this->collection; }
    public function getItem(): string { return $this->item; }

    public function getKey(): string { return $this->key; }
    public function setKey(string $key): static { $this->key = $key; return $this->touch(); }

    public function getData(): array { return $this->data; }

    public function getDelta(): ?array { return $this->delta; }
    public function setDelta(?array $delta): static { $this->delta = $delta; return $this->touch(); }

    public function getUserId(): ?string { return $this->userId; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
