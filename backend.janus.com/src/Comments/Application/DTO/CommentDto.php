<?php

declare(strict_types=1);

namespace App\Comments\Application\DTO;

use App\Comments\Domain\Entity\Comment;

final class CommentDto
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $collection,
        public readonly string  $item,
        public readonly string  $comment,
        public readonly string  $userId,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    public static function fromEntity(Comment $c): self
    {
        return new self(
            id:         (string) $c->getId(),
            collection: $c->getCollection(),
            item:       $c->getItem(),
            comment:    $c->getComment(),
            userId:     $c->getUserId(),
            createdAt:  $c->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:  $c->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'collection' => $this->collection,
            'item'       => $this->item,
            'comment'    => $this->comment,
            'user'       => $this->userId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
