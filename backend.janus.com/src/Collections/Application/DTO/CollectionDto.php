<?php

/**
 * @file CollectionDto.php
 *
 * Immutable data transfer object representing a collection's metadata for API responses.
 *
 * @package App\Collections\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Collections\Application\DTO;

use App\Collections\Domain\Entity\CollectionMeta;

/**
 * Read-only view of a CollectionMeta domain entity, shaped for JSON serialisation.
 *
 * Constructed exclusively via the fromEntity() factory to guarantee consistency
 * between the domain model and the response payload.
 */
final class CollectionDto
{
    /**
     * Constructor
     *
     * @param string      $id         UUID string of the collection record.
     * @param string      $name       Database table name and collection route handle.
     * @param string|null $label      Human-readable display label, or null.
     * @param string|null $icon       Icon identifier, or null.
     * @param string|null $note       Administrative note, or null.
     * @param bool        $hidden     Whether the collection is hidden from navigation.
     * @param bool        $singleton  Whether the collection is restricted to a single record.
     * @param string|null $sortField  Manual sort field name, or null.
     * @param string      $createdAt  ISO 8601 creation timestamp.
     * @param string|null $updatedAt  ISO 8601 last-mutation timestamp, or null.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $label,
        public readonly ?string $icon,
        public readonly ?string $note,
        public readonly bool    $hidden,
        public readonly bool    $singleton,
        public readonly ?string $sortField,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    /**
     * Constructs a CollectionDto from a CollectionMeta domain entity.
     *
     * @param  CollectionMeta $c The domain entity to convert.
     * @return self               A populated DTO ready for serialisation.
     */
    public static function fromEntity(CollectionMeta $c): self
    {
        return new self(
            id:        $c->getId(),
            name:      $c->getName(),
            label:     $c->getLabel(),
            icon:      $c->getIcon(),
            note:      $c->getNote(),
            hidden:    $c->isHidden(),
            singleton: $c->isSingleton(),
            sortField: $c->getSortField(),
            createdAt: $c->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $c->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * Serialises the DTO to an associative array for JSON encoding.
     *
     * @return array<string, mixed> Key-value map using snake_case keys.
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'collection' => $this->name,
            'label'      => $this->label,
            'icon'       => $this->icon,
            'note'       => $this->note,
            'hidden'     => $this->hidden,
            'singleton'  => $this->singleton,
            'sort_field' => $this->sortField,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
