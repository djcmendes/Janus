<?php

/**
 * @file CommentDto.php
 *
 * Data Transfer Object representing a comment in API responses.
 *
 * @package App\Comments\Application\DTO
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Application\DTO;

use App\Comments\Domain\Entity\Comment;

/**
 * Immutable DTO carrying comment data for serialisation into API responses.
 */
final class CommentDto
{
    /**
     * @param string      $id         UUID string of the comment.
     * @param string      $collection Name of the collection the comment belongs to.
     * @param string      $item       Identifier of the item the comment is attached to.
     * @param string      $comment    Text body of the comment.
     * @param string      $userId     UUID of the authoring user.
     * @param string      $createdAt  ISO-8601 creation timestamp.
     * @param string|null $updatedAt  ISO-8601 last-edit timestamp, or null if never edited.
     */
    public function __construct(
        public readonly string  $id,
        public readonly string  $collection,
        public readonly string  $item,
        public readonly string  $comment,
        public readonly string  $userId,
        public readonly string  $createdAt,
        public readonly ?string $updatedAt,
    ) {}

    /**
     * Constructs a CommentDto from a domain Comment entity.
     *
     * @param  Comment    $c The domain entity to convert.
     * @return self          A DTO populated from the entity's current state.
     */
    public static function fromEntity(Comment $c): self
    {
        return new self(
            id:         $c->getId(),
            collection: $c->getCollection(),
            item:       $c->getItem(),
            comment:    $c->getComment(),
            userId:     $c->getUserId(),
            createdAt:  $c->getCreatedAt()->format(\DateTimeInterface::ATOM),
            updatedAt:  $c->getUpdatedAt()?->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * Returns the DTO fields as an associative array suitable for JSON serialisation.
     *
     * @return array<string, string|null> Key-value map of all comment fields.
     */
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
