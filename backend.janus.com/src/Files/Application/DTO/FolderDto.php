<?php

declare(strict_types=1);

namespace App\Files\Application\DTO;

use App\Files\Domain\Entity\Folder;

final class FolderDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $name,
        public readonly ?string $parentId,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(Folder $f): self
    {
        return new self(
            id:        (string) $f->getId(),
            name:      $f->getName(),
            parentId:  $f->getParent() ? (string) $f->getParent()->getId() : null,
            createdAt: $f->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $f->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'parent'     => $this->parentId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
