<?php

declare(strict_types=1);

namespace App\Relations\Application\DTO;

use App\Relations\Domain\Entity\Relation;

final class RelationDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $manyCollection,
        public readonly string  $manyField,
        public readonly ?string $oneCollection,
        public readonly ?string $oneField,
        public readonly ?string $junctionCollection,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(Relation $r): self
    {
        return new self(
            id:                 (string) $r->getId(),
            manyCollection:     $r->getManyCollection(),
            manyField:          $r->getManyField(),
            oneCollection:      $r->getOneCollection(),
            oneField:           $r->getOneField(),
            junctionCollection: $r->getJunctionCollection(),
            createdAt:          $r->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:          $r->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'many_collection'    => $this->manyCollection,
            'many_field'         => $this->manyField,
            'one_collection'     => $this->oneCollection,
            'one_field'          => $this->oneField,
            'junction_collection'=> $this->junctionCollection,
            'created_at'         => $this->createdAt,
            'updated_at'         => $this->updatedAt,
        ];
    }
}
