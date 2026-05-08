<?php

/**
 * @file CollectionMeta.php
 *
 * Pure domain entity representing a CMS collection's metadata record.
 * Contains no framework or persistence dependencies.
 *
 * @package App\Collections\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Metadata descriptor for a user-defined CMS collection.
 *
 * A UUIDv7 string identifier is generated on construction, providing
 * chronological ordering without a separate timestamp index on the id column.
 * All Doctrine mapping concerns live exclusively in CollectionMetaEntity (Infrastructure layer).
 */
final class CollectionMeta
{
    /**
     * UUID string of the persisted record.
     * @var string
     */
    private string $id;

    /**
     * Database table name and collection handle used in routes.
     * @var string
     */
    private string $name;

    /**
     * Human-readable display label for the collection.
     * @var string|null
     */
    private ?string $label = null;

    /**
     * Icon identifier for UI display.
     * @var string|null
     */
    private ?string $icon = null;

    /**
     * Administrative note describing the purpose of this collection.
     * @var string|null
     */
    private ?string $note = null;

    /**
     * Whether the collection is hidden from navigation menus.
     * @var bool
     */
    private bool $hidden = false;

    /**
     * Whether the collection is restricted to a single record.
     * @var bool
     */
    private bool $singleton = false;

    /**
     * Field name used for manual drag-and-drop sorting.
     * @var string|null
     */
    private ?string $sortField = null;

    /**
     * Timestamp of when this collection was created.
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $createdAt;

    /**
     * Timestamp of the most recent mutation, or null if never mutated.
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * Constructor
     *
     * @param string $name The database table name and collection route handle (max 64 chars).
     */
    public function __construct(string $name)
    {
        $this->id        = (string) Uuid::v7();
        $this->name      = $name;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * Reconstructs a CollectionMeta from persisted data, bypassing the auto-generated
     * id and createdAt set by the constructor.
     *
     * Used exclusively by CollectionMetaMapper when converting from CollectionMetaEntity.
     *
     * @param string                  $id         UUID string of the persisted record.
     * @param string                  $name       Database table name and collection route handle.
     * @param string|null             $label      Human-readable display label, or null.
     * @param string|null             $icon       Icon identifier, or null.
     * @param string|null             $note       Administrative note, or null.
     * @param bool                    $hidden     Whether hidden from navigation.
     * @param bool                    $singleton  Whether restricted to a single record.
     * @param string|null             $sortField  Manual sort field name, or null.
     * @param DateTimeImmutable       $createdAt  Original creation timestamp from persistence.
     * @param DateTimeImmutable|null  $updatedAt  Last mutation timestamp, or null.
     *
     * @return self A fully-populated CollectionMeta with the given id and timestamps.
     */
    public static function reconstitute(
        string            $id,
        string            $name,
        ?string           $label,
        ?string           $icon,
        ?string           $note,
        bool              $hidden,
        bool              $singleton,
        ?string           $sortField,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ): self {
        $instance            = new self($name);
        $instance->id        = $id;
        $instance->createdAt = $createdAt;
        $instance->updatedAt = $updatedAt;
        $instance->label     = $label;
        $instance->icon      = $icon;
        $instance->note      = $note;
        $instance->hidden    = $hidden;
        $instance->singleton = $singleton;
        $instance->sortField = $sortField;

        return $instance;
    }

    /**
     * Returns the unique UUID of this collection record.
     *
     * @return string UUID v7 string.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the database table name and collection route handle.
     *
     * @return string Collection name (max 64 chars, alphanumeric and underscores).
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the human-readable display label, or null when not set.
     *
     * @return string|null Display label, or null.
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Sets the human-readable display label.
     *
     * @param  string|null $label Display label, or null to clear.
     * @return static
     */
    public function setLabel(?string $label): static
    {
        $this->label = $label;
        return $this->touch();
    }

    /**
     * Returns the icon identifier for UI display, or null when not set.
     *
     * @return string|null Icon identifier, or null.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Sets the icon identifier for UI display.
     *
     * @param  string|null $icon Icon identifier, or null to clear.
     * @return static
     */
    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this->touch();
    }

    /**
     * Returns the administrative note, or null when not set.
     *
     * @return string|null Administrative note, or null.
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Sets the administrative note.
     *
     * @param  string|null $note Administrative note, or null to clear.
     * @return static
     */
    public function setNote(?string $note): static
    {
        $this->note = $note;
        return $this->touch();
    }

    /**
     * Returns true when the collection is hidden from navigation menus.
     *
     * @return bool Hidden flag.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Sets the navigation visibility flag.
     *
     * @param  bool $hidden True to hide from navigation, false to show.
     * @return static
     */
    public function setHidden(bool $hidden): static
    {
        $this->hidden = $hidden;
        return $this->touch();
    }

    /**
     * Returns true when the collection is restricted to a single record.
     *
     * @return bool Singleton flag.
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * Sets the singleton flag.
     *
     * @param  bool $singleton True to restrict to a single record, false for many.
     * @return static
     */
    public function setSingleton(bool $singleton): static
    {
        $this->singleton = $singleton;
        return $this->touch();
    }

    /**
     * Returns the field name used for manual drag-and-drop sorting, or null when not configured.
     *
     * @return string|null Sort field name, or null.
     */
    public function getSortField(): ?string
    {
        return $this->sortField;
    }

    /**
     * Sets the field name used for manual drag-and-drop sorting.
     *
     * @param  string|null $sortField Field name, or null to disable sorting.
     * @return static
     */
    public function setSortField(?string $sortField): static
    {
        $this->sortField = $sortField;
        return $this->touch();
    }

    /**
     * Returns the UTC timestamp of when the collection was created.
     *
     * @return DateTimeImmutable Immutable datetime in UTC.
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Returns the UTC timestamp of the most recent mutation, or null if never mutated.
     *
     * @return DateTimeImmutable|null Immutable datetime in UTC, or null.
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Serialises the entity to an associative array for JSON encoding.
     *
     * @return array<string, mixed> Key-value map of all entity fields.
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'label'      => $this->label,
            'icon'       => $this->icon,
            'note'       => $this->note,
            'hidden'     => $this->hidden,
            'singleton'  => $this->singleton,
            'sort_field' => $this->sortField,
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
            'updated_at' => $this->updatedAt?->format(DateTimeInterface::ATOM),
        ];
    }

    /**
     * Updates the updatedAt timestamp to the current time.
     *
     * @return static
     */
    private function touch(): static
    {
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
}
