<?php

/**
 * @file FieldMeta.php
 *
 * Pure domain entity representing a single field definition within a collection.
 * Zero framework dependencies — all persistence concerns live in FieldMetaEntity.
 *
 * @package App\Fields\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Domain\Entity;

use App\Fields\Domain\Enum\FieldType;
use Symfony\Component\Uid\Uuid;

/**
 * Metadata record for a single field column within a collection.
 *
 * The constructor generates a fresh UUIDv7 identity and sets createdAt.
 * Use reconstitute() to reload an existing record from persistence without
 * generating a new ID or resetting timestamps.
 *
 * Mutating setters automatically refresh updatedAt via the private touch() method.
 *
 * Note: the property name $interface uses PHP's reserved word — accessed via getInterface().
 */
final class FieldMeta
{
    /** @var string UUIDv7 string primary key. */
    private string $id;

    /** @var string Name of the collection (database table) this field belongs to. */
    private string $collection;

    /** @var string Column name within the table. */
    private string $field;

    /** @var FieldType Data type of this field. */
    private FieldType $type;

    /** @var string|null Optional human-readable display label. */
    private ?string $label = null;

    /** @var string|null Optional descriptive note shown in the Admin UI. */
    private ?string $note = null;

    /** @var bool Whether this field is required on create/update. */
    private bool $required = false;

    /** @var bool Whether this field is read-only in the Admin UI. */
    private bool $readonly = false;

    /** @var bool Whether this field is hidden in the Admin UI. */
    private bool $hidden = false;

    /** @var int Display order within the collection's field list. */
    private int $sortOrder = 0;

    /** @var string|null Admin UI component identifier for this field. */
    private ?string $interface = null;

    /** @var array<string, mixed>|null JSON options for the Admin UI component. */
    private ?array $options = null;

    /** @var \DateTimeImmutable Creation timestamp (UTC). */
    private \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable|null Last-modification timestamp (UTC), or null if never mutated. */
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Creates a new FieldMeta record with a fresh UUIDv7 identity.
     *
     * @param string    $collection Collection (table) name.
     * @param string    $field      Column name.
     * @param FieldType $type       Data type of the field.
     */
    public function __construct(string $collection, string $field, FieldType $type)
    {
        $this->id         = Uuid::v7()->toRfc4122();
        $this->collection = $collection;
        $this->field      = $field;
        $this->type       = $type;
        $this->createdAt  = new \DateTimeImmutable();
    }

    /**
     * Reloads a persisted FieldMeta record without generating a new ID or resetting timestamps.
     *
     * @param string                   $id         Existing UUID string.
     * @param string                   $collection Collection name.
     * @param string                   $field      Column name.
     * @param FieldType                $type       Data type.
     * @param string|null              $label      Display label.
     * @param string|null              $note       Descriptive note.
     * @param bool                     $required   Required flag.
     * @param bool                     $readonly   Read-only flag.
     * @param bool                     $hidden     Hidden flag.
     * @param int                      $sortOrder  Display order.
     * @param string|null              $interface  UI component identifier.
     * @param array<string,mixed>|null $options    UI component options.
     * @param \DateTimeImmutable       $createdAt  Original creation timestamp.
     * @param \DateTimeImmutable|null  $updatedAt  Last-modification timestamp, or null.
     *
     * @return self
     */
    public static function reconstitute(
        string $id,
        string $collection,
        string $field,
        FieldType $type,
        ?string $label,
        ?string $note,
        bool $required,
        bool $readonly,
        bool $hidden,
        int $sortOrder,
        ?string $interface,
        ?array $options,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt,
    ): self {
        $self             = new self($collection, $field, $type);
        $self->id         = $id;
        $self->label      = $label;
        $self->note       = $note;
        $self->required   = $required;
        $self->readonly   = $readonly;
        $self->hidden     = $hidden;
        $self->sortOrder  = $sortOrder;
        $self->interface  = $interface;
        $self->options    = $options;
        $self->createdAt  = $createdAt;
        $self->updatedAt  = $updatedAt;
        return $self;
    }

    /**
     * Returns the UUIDv7 string primary key.
     *
     * @return string UUID string.
     */
    public function getId(): string { return $this->id; }

    /**
     * Returns the collection (table) name.
     *
     * @return string Collection name.
     */
    public function getCollection(): string { return $this->collection; }

    /**
     * Returns the column name within the collection.
     *
     * @return string Column name.
     */
    public function getField(): string { return $this->field; }

    /**
     * Returns the data type of this field.
     *
     * @return FieldType Enum case.
     */
    public function getType(): FieldType { return $this->type; }

    /**
     * Sets the data type and refreshes updatedAt.
     *
     * @param  FieldType $type New data type.
     * @return static
     */
    public function setType(FieldType $type): static { $this->type = $type; return $this->touch(); }

    /**
     * Returns the optional display label, or null when not set.
     *
     * @return string|null Display label.
     */
    public function getLabel(): ?string { return $this->label; }

    /**
     * Sets the display label and refreshes updatedAt.
     *
     * @param  string|null $label Display label, or null to clear.
     * @return static
     */
    public function setLabel(?string $label): static { $this->label = $label; return $this->touch(); }

    /**
     * Returns the optional descriptive note, or null when not set.
     *
     * @return string|null Descriptive note.
     */
    public function getNote(): ?string { return $this->note; }

    /**
     * Sets the descriptive note and refreshes updatedAt.
     *
     * @param  string|null $note Descriptive note, or null to clear.
     * @return static
     */
    public function setNote(?string $note): static { $this->note = $note; return $this->touch(); }

    /**
     * Returns whether this field is required.
     *
     * @return bool True when required.
     */
    public function isRequired(): bool { return $this->required; }

    /**
     * Sets the required flag and refreshes updatedAt.
     *
     * @param  bool $required New required state.
     * @return static
     */
    public function setRequired(bool $required): static { $this->required = $required; return $this->touch(); }

    /**
     * Returns whether this field is read-only in the Admin UI.
     *
     * @return bool True when read-only.
     */
    public function isReadonly(): bool { return $this->readonly; }

    /**
     * Sets the read-only flag and refreshes updatedAt.
     *
     * @param  bool $readonly New read-only state.
     * @return static
     */
    public function setReadonly(bool $readonly): static { $this->readonly = $readonly; return $this->touch(); }

    /**
     * Returns whether this field is hidden in the Admin UI.
     *
     * @return bool True when hidden.
     */
    public function isHidden(): bool { return $this->hidden; }

    /**
     * Sets the hidden flag and refreshes updatedAt.
     *
     * @param  bool $hidden New hidden state.
     * @return static
     */
    public function setHidden(bool $hidden): static { $this->hidden = $hidden; return $this->touch(); }

    /**
     * Returns the display sort order within the collection's field list.
     *
     * @return int Sort order index.
     */
    public function getSortOrder(): int { return $this->sortOrder; }

    /**
     * Sets the display sort order and refreshes updatedAt.
     *
     * @param  int $sortOrder New sort order.
     * @return static
     */
    public function setSortOrder(int $sortOrder): static { $this->sortOrder = $sortOrder; return $this->touch(); }

    /**
     * Returns the Admin UI component identifier, or null when not set.
     *
     * @return string|null UI component identifier.
     */
    public function getInterface(): ?string { return $this->interface; }

    /**
     * Sets the Admin UI component identifier and refreshes updatedAt.
     *
     * @param  string|null $interface UI component identifier, or null to clear.
     * @return static
     */
    public function setInterface(?string $interface): static { $this->interface = $interface; return $this->touch(); }

    /**
     * Returns the JSON options array for the Admin UI component, or null when not configured.
     *
     * @return array<string, mixed>|null Options map, or null.
     */
    public function getOptions(): ?array { return $this->options; }

    /**
     * Sets the JSON options and refreshes updatedAt.
     *
     * @param  array<string, mixed>|null $options Options map, or null to clear.
     * @return static
     */
    public function setOptions(?array $options): static { $this->options = $options; return $this->touch(); }

    /**
     * Returns the UTC creation timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC creation timestamp.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * Returns the UTC last-modification timestamp, or null if never mutated.
     *
     * @return \DateTimeImmutable|null Immutable UTC last-modification timestamp, or null.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    /**
     * Refreshes updatedAt to the current UTC instant and returns $this for fluent chaining.
     *
     * @return static
     */
    private function touch(): static
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }
}
