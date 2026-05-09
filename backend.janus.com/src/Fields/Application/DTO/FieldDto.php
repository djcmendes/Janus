<?php

/**
 * @file FieldDto.php
 *
 * Read model for a single field metadata entry.
 * Produced by query handlers and serialized to the HTTP response envelope.
 *
 * @package App\Fields\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Fields\Application\DTO;

use App\Fields\Domain\Entity\FieldMeta;

/**
 * Immutable read model representing a single FieldMeta record.
 *
 * Created via the static fromEntity() factory; serialized via toArray().
 */
final class FieldDto
{
    /**
     * @param string                   $id         UUIDv7 string.
     * @param string                   $collection Collection name.
     * @param string                   $field      Column name.
     * @param string                   $type       FieldType backing value.
     * @param string|null              $label      Display label, or null.
     * @param string|null              $note       Descriptive note, or null.
     * @param bool                     $required   Required flag.
     * @param bool                     $readonly   Read-only flag.
     * @param bool                     $hidden     Hidden flag.
     * @param int                      $sortOrder  Display order.
     * @param string|null              $interface  UI component identifier, or null.
     * @param array<string,mixed>|null $options    UI component options, or null.
     * @param string                   $createdAt  ISO 8601 creation timestamp.
     * @param string|null              $updatedAt  ISO 8601 last-modification timestamp, or null.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $collection,
        public readonly string  $field,
        public readonly string  $type,
        public readonly ?string $label,
        public readonly ?string $note,
        public readonly bool    $required,
        public readonly bool    $readonly,
        public readonly bool    $hidden,
        public readonly int     $sortOrder,
        public readonly ?string $interface,
        public readonly ?array  $options,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    /**
     * Constructs a FieldDto from a domain FieldMeta entity.
     *
     * @param  FieldMeta $f Source domain entity.
     * @return self          Populated read model.
     */
    public static function fromEntity(FieldMeta $f): self
    {
        return new self(
            id:         $f->getId(),
            collection: $f->getCollection(),
            field:      $f->getField(),
            type:       $f->getType()->value,
            label:      $f->getLabel(),
            note:       $f->getNote(),
            required:   $f->isRequired(),
            readonly:   $f->isReadonly(),
            hidden:     $f->isHidden(),
            sortOrder:  $f->getSortOrder(),
            interface:  $f->getInterface(),
            options:    $f->getOptions(),
            createdAt:  $f->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:  $f->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * Serializes this DTO to the standard JSON response shape.
     *
     * @return array<string, mixed> Associative array ready for json_encode.
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'collection' => $this->collection,
            'field'      => $this->field,
            'type'       => $this->type,
            'label'      => $this->label,
            'note'       => $this->note,
            'required'   => $this->required,
            'readonly'   => $this->readonly,
            'hidden'     => $this->hidden,
            'sort'       => $this->sortOrder,
            'interface'  => $this->interface,
            'options'    => $this->options,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
