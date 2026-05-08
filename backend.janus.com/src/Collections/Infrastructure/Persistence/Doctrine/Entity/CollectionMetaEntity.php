<?php

/**
 * @file CollectionMetaEntity.php
 *
 * Doctrine ORM persistence model for the `janus_collections` table.
 * This class is the sole owner of all database-mapping concerns for collection metadata records.
 * Domain logic lives exclusively in CollectionMeta (Domain\Entity).
 *
 * @package App\Collections\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine entity mapping collection metadata records to the `janus_collections` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain CollectionMeta class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'janus_collections')]
#[ORM\UniqueConstraint(name: 'UNIQ_COLLECTION_NAME', columns: ['name'])]
class CollectionMetaEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 64, unique: true)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    #[ORM\Column]
    private bool $hidden = false;

    #[ORM\Column]
    private bool $singleton = false;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $sortField = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Returns the UUID primary key of this record.
     *
     * @return Uuid|null Doctrine-managed UUID value object, or null before first persist.
     */
    public function getId(): ?Uuid { return $this->id; }

    /**
     * Sets the UUID primary key.
     *
     * @param  Uuid $id The UUID to assign as the primary key.
     * @return static
     */
    public function setId(Uuid $id): static { $this->id = $id; return $this; }

    /**
     * Returns the database table name and collection route handle.
     *
     * @return string Collection name.
     */
    public function getName(): string { return $this->name; }

    /**
     * Sets the database table name and collection route handle.
     *
     * @param  string $name Collection name (max 64 chars, alphanumeric and underscores).
     * @return static
     */
    public function setName(string $name): static { $this->name = $name; return $this; }

    /**
     * Returns the human-readable display label, or null when not set.
     *
     * @return string|null Display label, or null.
     */
    public function getLabel(): ?string { return $this->label; }

    /**
     * Sets the human-readable display label.
     *
     * @param  string|null $label Display label, or null to clear.
     * @return static
     */
    public function setLabel(?string $label): static { $this->label = $label; return $this; }

    /**
     * Returns the icon identifier for UI display, or null when not set.
     *
     * @return string|null Icon identifier, or null.
     */
    public function getIcon(): ?string { return $this->icon; }

    /**
     * Sets the icon identifier for UI display.
     *
     * @param  string|null $icon Icon identifier, or null to clear.
     * @return static
     */
    public function setIcon(?string $icon): static { $this->icon = $icon; return $this; }

    /**
     * Returns the administrative note, or null when not set.
     *
     * @return string|null Administrative note, or null.
     */
    public function getNote(): ?string { return $this->note; }

    /**
     * Sets the administrative note.
     *
     * @param  string|null $note Administrative note, or null to clear.
     * @return static
     */
    public function setNote(?string $note): static { $this->note = $note; return $this; }

    /**
     * Returns true when the collection is hidden from navigation menus.
     *
     * @return bool Hidden flag.
     */
    public function isHidden(): bool { return $this->hidden; }

    /**
     * Sets the navigation visibility flag.
     *
     * @param  bool $hidden True to hide from navigation, false to show.
     * @return static
     */
    public function setHidden(bool $hidden): static { $this->hidden = $hidden; return $this; }

    /**
     * Returns true when the collection is restricted to a single record.
     *
     * @return bool Singleton flag.
     */
    public function isSingleton(): bool { return $this->singleton; }

    /**
     * Sets the singleton flag.
     *
     * @param  bool $singleton True to restrict to a single record, false for many.
     * @return static
     */
    public function setSingleton(bool $singleton): static { $this->singleton = $singleton; return $this; }

    /**
     * Returns the field name used for manual drag-and-drop sorting, or null when not configured.
     *
     * @return string|null Sort field name, or null.
     */
    public function getSortField(): ?string { return $this->sortField; }

    /**
     * Sets the field name used for manual drag-and-drop sorting.
     *
     * @param  string|null $sortField Field name, or null to disable sorting.
     * @return static
     */
    public function setSortField(?string $sortField): static { $this->sortField = $sortField; return $this; }

    /**
     * Returns the UTC timestamp of when the collection was created.
     *
     * @return \DateTimeImmutable Immutable datetime in UTC.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * Sets the UTC timestamp of when the collection was created.
     *
     * @param  \DateTimeImmutable $createdAt Immutable datetime in UTC.
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /**
     * Returns the UTC timestamp of the most recent mutation, or null if never mutated.
     *
     * @return \DateTimeImmutable|null Immutable datetime in UTC, or null.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    /**
     * Sets the UTC timestamp of the most recent mutation.
     *
     * @param  \DateTimeImmutable|null $updatedAt Immutable datetime in UTC, or null to clear.
     * @return static
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
