<?php

declare(strict_types=1);

namespace App\Collections\Application\DTO;

use App\Collections\Domain\Entity\CollectionMeta;

final class CollectionDto
{
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

    public static function fromEntity(CollectionMeta $c): self
    {
        return new self(
            id:        (string) $c->getId(),
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
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
