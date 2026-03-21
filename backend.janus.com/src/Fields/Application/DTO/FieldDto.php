<?php

declare(strict_types=1);

namespace App\Fields\Application\DTO;

use App\Fields\Domain\Entity\FieldMeta;

final class FieldDto
{
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
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(FieldMeta $f): self
    {
        return new self(
            id:         (string) $f->getId(),
            collection: $f->getCollection(),
            field:      $f->getField(),
            type:       $f->getType()->value,
            label:      $f->getLabel(),
            note:       $f->getNote(),
            required:   $f->isRequired(),
            readonly:   $f->isReadonly(),
            hidden:     $f->isHidden(),
            sortOrder:  $f->getSortOrder(),
            createdAt:  $f->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:  $f->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

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
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
