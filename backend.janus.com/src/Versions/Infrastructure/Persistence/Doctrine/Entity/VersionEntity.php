<?php

/**
 * @file VersionEntity.php
 *
 * Doctrine ORM persistence model for the `versions` table.
 * This class is the sole owner of all database-mapping concerns for version records.
 * Domain logic lives exclusively in Version (Domain\Entity).
 *
 * @package App\Versions\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine entity mapping version snapshot records to the `versions` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain Version class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'versions')]
#[ORM\UniqueConstraint(name: 'uniq_version_collection_item_key', columns: ['collection', 'item', 'version_key'])]
#[ORM\Index(name: 'idx_version_collection_item', columns: ['collection', 'item'])]
class VersionEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 64)]
    private string $collection;

    #[ORM\Column(length: 36)]
    private string $item;

    #[ORM\Column(name: 'version_key', length: 64)]
    private string $key;

    #[ORM\Column(type: 'json')]
    private array $data;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $delta = null;

    #[ORM\Column(length: 36, nullable: true)]
    private ?string $userId = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Returns the UUID primary key of this record.
     *
     * @return Uuid Doctrine-managed UUID value object.
     */
    public function getId(): Uuid { return $this->id; }

    /**
     * Sets the UUID primary key.
     *
     * @param  Uuid $id The UUID to assign as the primary key.
     * @return static
     */
    public function setId(Uuid $id): static { $this->id = $id; return $this; }

    /**
     * Returns the name of the collection this version targets.
     *
     * @return string Collection name.
     */
    public function getCollection(): string { return $this->collection; }

    /**
     * Sets the collection name.
     *
     * @param  string $collection Collection name to store.
     * @return static
     */
    public function setCollection(string $collection): static { $this->collection = $collection; return $this; }

    /**
     * Returns the UUID/ID of the versioned item within the collection.
     *
     * @return string Item identifier.
     */
    public function getItem(): string { return $this->item; }

    /**
     * Sets the item identifier.
     *
     * @param  string $item Item identifier to store.
     * @return static
     */
    public function setItem(string $item): static { $this->item = $item; return $this; }

    /**
     * Returns the human-readable version label stored in the `version_key` column.
     *
     * @return string Version label (e.g. "main", "draft").
     */
    public function getKey(): string { return $this->key; }

    /**
     * Sets the version label.
     *
     * @param  string $key Version label to store.
     * @return static
     */
    public function setKey(string $key): static { $this->key = $key; return $this; }

    /**
     * Returns the full item data snapshot.
     *
     * @return array<string, mixed> Associative array of item fields at snapshot time.
     */
    public function getData(): array { return $this->data; }

    /**
     * Sets the item data snapshot.
     *
     * @param  array<string, mixed> $data Data snapshot to store.
     * @return static
     */
    public function setData(array $data): static { $this->data = $data; return $this; }

    /**
     * Returns the diff against the previous version, or null when not available.
     *
     * @return array<string, mixed>|null Field-level diff, or null when not computed.
     */
    public function getDelta(): ?array { return $this->delta; }

    /**
     * Sets the diff against the previous version.
     *
     * @param  array<string, mixed>|null $delta Field-level diff, or null to clear.
     * @return static
     */
    public function setDelta(?array $delta): static { $this->delta = $delta; return $this; }

    /**
     * Returns the UUID of the user who created this version, or null for system operations.
     *
     * @return string|null User UUID, or null when no user is associated.
     */
    public function getUserId(): ?string { return $this->userId; }

    /**
     * Sets the user UUID.
     *
     * @param  string|null $userId User UUID, or null to clear.
     * @return static
     */
    public function setUserId(?string $userId): static { $this->userId = $userId; return $this; }

    /**
     * Returns the UTC timestamp of when this version was created.
     *
     * @return \DateTimeImmutable Immutable datetime in UTC.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * Sets the creation timestamp.
     *
     * @param  \DateTimeImmutable $createdAt Creation timestamp to store.
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /**
     * Returns the UTC timestamp of the last mutation to mutable fields, or null if never updated.
     *
     * @return \DateTimeImmutable|null Immutable datetime in UTC, or null when unmodified.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    /**
     * Sets the last-mutation timestamp.
     *
     * @param  \DateTimeImmutable|null $updatedAt Mutation timestamp, or null to clear.
     * @return static
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
