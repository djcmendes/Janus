<?php

/**
 * @file Version.php
 *
 * Pure domain entity representing a named content snapshot for a collection item.
 * Contains no framework or persistence dependencies.
 *
 * @package App\Versions\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Versions\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Named snapshot of a content item at a specific point in time.
 *
 * A UUIDv7 string identifier is generated on construction.
 * The `collection` + `item` + `key` triplet must be unique: only one version
 * with a given name can exist per item at a time.
 * All Doctrine mapping concerns live exclusively in VersionEntity (Infrastructure layer).
 */
final class Version
{
    /**
     * UUID string of the persisted record.
     * @var string
     */
    private string $id;

    /**
     * Name of the collection the versioned item belongs to.
     * @var string
     */
    private string $collection;

    /**
     * UUID/ID of the item within the collection being versioned.
     * @var string
     */
    private string $item;

    /**
     * Human-readable version name (e.g. "main", "draft", "v1.0").
     * @var string
     */
    private string $key;

    /**
     * Full item data snapshot stored as an associative array.
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * Diff vs. the previous version, or null when not provided.
     * @var array<string, mixed>|null
     */
    private ?array $delta;

    /**
     * UUID of the user who created this version, or null for system operations.
     * @var string|null
     */
    private ?string $userId;

    /**
     * Timestamp of when this version was created.
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $createdAt;

    /**
     * Timestamp of the last mutation to mutable fields, or null if never updated.
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $updatedAt;

    /**
     * @param string               $collection Collection the item belongs to.
     * @param string               $item       UUID/ID of the item being versioned.
     * @param string               $key        Human-readable version label.
     * @param array<string, mixed> $data       Full data snapshot of the item.
     * @param array<string, mixed>|null $delta  Optional diff against the previous version.
     * @param string|null          $userId     UUID of the user creating this version.
     */
    public function __construct(
        string  $collection,
        string  $item,
        string  $key,
        array   $data,
        ?array  $delta  = null,
        ?string $userId = null,
    ) {
        $this->id         = (string) Uuid::v7();
        $this->collection = $collection;
        $this->item       = $item;
        $this->key        = $key;
        $this->data       = $data;
        $this->delta      = $delta;
        $this->userId     = $userId;
        $this->createdAt  = new DateTimeImmutable();
        $this->updatedAt  = null;
    }

    /**
     * Reconstructs a Version from persisted data, bypassing the auto-generated
     * id and createdAt set by the constructor.
     *
     * Used exclusively by VersionMapper when converting from VersionEntity.
     *
     * @param string                    $id         UUID string of the persisted record.
     * @param string                    $collection Collection the item belongs to.
     * @param string                    $item       UUID/ID of the versioned item.
     * @param string                    $key        Human-readable version label.
     * @param array<string, mixed>      $data       Full item data snapshot.
     * @param array<string, mixed>|null $delta      Optional diff against previous version.
     * @param string|null               $userId     User UUID, or null for system operations.
     * @param DateTimeImmutable         $createdAt  Original creation timestamp from persistence.
     * @param DateTimeImmutable|null    $updatedAt  Last mutation timestamp, or null if never updated.
     *
     * @return self A fully-populated Version with the given id and timestamps.
     */
    public static function reconstitute(
        string            $id,
        string            $collection,
        string            $item,
        string            $key,
        array             $data,
        ?array            $delta,
        ?string           $userId,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ): self {
        $instance            = new self($collection, $item, $key, $data, $delta, $userId);
        $instance->id        = $id;
        $instance->createdAt = $createdAt;
        $instance->updatedAt = $updatedAt;

        return $instance;
    }

    /**
     * Returns the unique UUID of this version record.
     *
     * @return string UUIDv7 string.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the name of the collection this version targets.
     *
     * @return string Collection name.
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * Returns the UUID/ID of the item within the collection that was versioned.
     *
     * @return string Item identifier.
     */
    public function getItem(): string
    {
        return $this->item;
    }

    /**
     * Returns the human-readable label that identifies this version (e.g. "main", "draft").
     *
     * @return string Version label.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Sets a new version label and records the mutation timestamp.
     *
     * @param  string $key New human-readable label for this version.
     * @return static
     */
    public function setKey(string $key): static
    {
        $this->key = $key;
        return $this->touch();
    }

    /**
     * Returns the full item data snapshot stored in this version.
     *
     * @return array<string, mixed> Associative array of item fields at snapshot time.
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns the diff against the previous version, or null when not available.
     *
     * @return array<string, mixed>|null Field-level diff, or null when not computed.
     */
    public function getDelta(): ?array
    {
        return $this->delta;
    }

    /**
     * Sets the diff against the previous version and records the mutation timestamp.
     *
     * @param  array<string, mixed>|null $delta Field-level diff, or null to clear.
     * @return static
     */
    public function setDelta(?array $delta): static
    {
        $this->delta = $delta;
        return $this->touch();
    }

    /**
     * Returns the UUID of the user who created this version, or null for system operations.
     *
     * @return string|null User UUID, or null when no user is associated.
     */
    public function getUserId(): ?string
    {
        return $this->userId;
    }

    /**
     * Returns the UTC timestamp of when this version was created.
     *
     * @return DateTimeImmutable Immutable datetime in UTC.
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Returns the UTC timestamp of the last mutation to mutable fields, or null if never updated.
     *
     * @return DateTimeImmutable|null Immutable datetime in UTC, or null when unmodified.
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Updates the updatedAt timestamp to reflect a mutation to mutable fields.
     *
     * @return static
     */
    private function touch(): static
    {
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
}
