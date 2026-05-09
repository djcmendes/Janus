<?php

/**
 * @file FieldMetaEntity.php
 *
 * Doctrine ORM persistence model for the `janus_fields` table.
 * This class is the sole owner of all database-mapping concerns for field records.
 * Domain logic lives exclusively in FieldMeta (Domain\Entity).
 *
 * @package App\Fields\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Infrastructure\Persistence\Doctrine\Entity;

use App\Fields\Domain\Enum\FieldType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity mapping field metadata records to the `janus_fields` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain FieldMeta class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'janus_fields')]
#[ORM\UniqueConstraint(name: 'UNIQ_FIELD_COLLECTION_FIELD', columns: ['collection', 'field'])]
class FieldMetaEntity
{
    /** @var string UUIDv7 string primary key. */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    /** @var string Collection (table) name. */
    #[ORM\Column(length: 64)]
    private string $collection;

    /** @var string Column name within the collection. */
    #[ORM\Column(length: 64)]
    private string $field;

    /** @var FieldType Data type of this field. */
    #[ORM\Column(length: 30, enumType: FieldType::class)]
    private FieldType $type;

    /** @var string|null Optional display label. */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $label = null;

    /** @var string|null Optional descriptive note. */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $note = null;

    /** @var bool Whether the field is required on input. */
    #[ORM\Column]
    private bool $required = false;

    /** @var bool Whether the field is read-only in Admin UI. */
    #[ORM\Column]
    private bool $readonly = false;

    /** @var bool Whether the field is hidden in Admin UI. */
    #[ORM\Column]
    private bool $hidden = false;

    /** @var int Display order within the collection's field list. */
    #[ORM\Column]
    private int $sortOrder = 0;

    /** @var string|null Admin UI component identifier. */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $interface = null;

    /** @var array<string, mixed>|null JSON options for the Admin UI component. */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $options = null;

    /** @var \DateTimeImmutable Creation timestamp (UTC). */
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable|null Last-modification timestamp (UTC), or null if never mutated. */
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Returns the UUIDv7 string primary key.
     *
     * @return string UUID string.
     */
    public function getId(): string { return $this->id; }

    /**
     * Sets the primary key.
     *
     * @param  string $id UUID string.
     * @return static
     */
    public function setId(string $id): static { $this->id = $id; return $this; }

    /**
     * Returns the collection (table) name.
     *
     * @return string Collection name.
     */
    public function getCollection(): string { return $this->collection; }

    /**
     * Sets the collection name.
     *
     * @param  string $collection Collection name.
     * @return static
     */
    public function setCollection(string $collection): static { $this->collection = $collection; return $this; }

    /**
     * Returns the column name.
     *
     * @return string Column name.
     */
    public function getField(): string { return $this->field; }

    /**
     * Sets the column name.
     *
     * @param  string $field Column name.
     * @return static
     */
    public function setField(string $field): static { $this->field = $field; return $this; }

    /**
     * Returns the data type enum.
     *
     * @return FieldType Data type case.
     */
    public function getType(): FieldType { return $this->type; }

    /**
     * Sets the data type enum.
     *
     * @param  FieldType $type Data type case.
     * @return static
     */
    public function setType(FieldType $type): static { $this->type = $type; return $this; }

    /**
     * Returns the optional display label.
     *
     * @return string|null Display label, or null.
     */
    public function getLabel(): ?string { return $this->label; }

    /**
     * Sets the display label.
     *
     * @param  string|null $label Display label, or null to clear.
     * @return static
     */
    public function setLabel(?string $label): static { $this->label = $label; return $this; }

    /**
     * Returns the optional descriptive note.
     *
     * @return string|null Note, or null.
     */
    public function getNote(): ?string { return $this->note; }

    /**
     * Sets the descriptive note.
     *
     * @param  string|null $note Note, or null to clear.
     * @return static
     */
    public function setNote(?string $note): static { $this->note = $note; return $this; }

    /**
     * Returns whether the field is required.
     *
     * @return bool True when required.
     */
    public function isRequired(): bool { return $this->required; }

    /**
     * Sets the required flag.
     *
     * @param  bool $required Required state.
     * @return static
     */
    public function setRequired(bool $required): static { $this->required = $required; return $this; }

    /**
     * Returns whether the field is read-only in Admin UI.
     *
     * @return bool True when read-only.
     */
    public function isReadonly(): bool { return $this->readonly; }

    /**
     * Sets the read-only flag.
     *
     * @param  bool $readonly Read-only state.
     * @return static
     */
    public function setReadonly(bool $readonly): static { $this->readonly = $readonly; return $this; }

    /**
     * Returns whether the field is hidden in Admin UI.
     *
     * @return bool True when hidden.
     */
    public function isHidden(): bool { return $this->hidden; }

    /**
     * Sets the hidden flag.
     *
     * @param  bool $hidden Hidden state.
     * @return static
     */
    public function setHidden(bool $hidden): static { $this->hidden = $hidden; return $this; }

    /**
     * Returns the display sort order.
     *
     * @return int Sort order index.
     */
    public function getSortOrder(): int { return $this->sortOrder; }

    /**
     * Sets the display sort order.
     *
     * @param  int $sortOrder Sort order index.
     * @return static
     */
    public function setSortOrder(int $sortOrder): static { $this->sortOrder = $sortOrder; return $this; }

    /**
     * Returns the Admin UI component identifier, or null.
     *
     * @return string|null Component identifier.
     */
    public function getInterface(): ?string { return $this->interface; }

    /**
     * Sets the Admin UI component identifier.
     *
     * @param  string|null $interface Component identifier, or null to clear.
     * @return static
     */
    public function setInterface(?string $interface): static { $this->interface = $interface; return $this; }

    /**
     * Returns the JSON options array for the Admin UI component, or null.
     *
     * @return array<string, mixed>|null Options map.
     */
    public function getOptions(): ?array { return $this->options; }

    /**
     * Sets the JSON options.
     *
     * @param  array<string, mixed>|null $options Options map, or null to clear.
     * @return static
     */
    public function setOptions(?array $options): static { $this->options = $options; return $this; }

    /**
     * Returns the UTC creation timestamp.
     *
     * @return \DateTimeImmutable Immutable UTC creation timestamp.
     */
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /**
     * Sets the UTC creation timestamp.
     *
     * @param  \DateTimeImmutable $createdAt Immutable UTC creation timestamp.
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /**
     * Returns the UTC last-modification timestamp, or null if never mutated.
     *
     * @return \DateTimeImmutable|null Immutable last-modification timestamp, or null.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }

    /**
     * Sets the UTC last-modification timestamp.
     *
     * @param  \DateTimeImmutable|null $updatedAt Immutable last-modification timestamp, or null.
     * @return static
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }
}
