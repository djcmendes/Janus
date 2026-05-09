<?php

/**
 * @file Comment.php
 *
 * Pure domain entity representing a single user comment on a collection item.
 * Contains no framework or persistence dependencies.
 *
 * @package App\Comments\Domain\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Domain\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

/**
 * A user-authored comment attached to a specific item in any collection.
 *
 * A UUIDv7 string identifier is generated on construction. All Doctrine
 * mapping concerns live exclusively in CommentEntity (Infrastructure layer).
 */
final class Comment
{
    /**
     * UUID string of the persisted record.
     * @var string
     */
    private string $id;

    /**
     * Name of the collection this comment belongs to.
     * @var string
     */
    private string $collection;

    /**
     * Identifier of the item this comment is attached to.
     * @var string
     */
    private string $item;

    /**
     * Text body of the comment.
     * @var string
     */
    private string $comment;

    /**
     * UUID of the user who authored this comment.
     * @var string
     */
    private string $userId;

    /**
     * Timestamp of when the comment was first created.
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $createdAt;

    /**
     * Timestamp of the last edit, or null if never edited.
     * @var DateTimeImmutable|null
     */
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @param string $collection Name of the collection the comment belongs to.
     * @param string $item       Identifier of the item the comment is attached to.
     * @param string $comment    Text body of the comment.
     * @param string $userId     UUID of the authoring user.
     */
    public function __construct(
        string $collection,
        string $item,
        string $comment,
        string $userId,
    ) {
        $this->id         = (string) Uuid::v7();
        $this->collection = $collection;
        $this->item       = $item;
        $this->comment    = $comment;
        $this->userId     = $userId;
        $this->createdAt  = new DateTimeImmutable();
    }

    /**
     * Reconstructs a Comment from persisted data, bypassing the auto-generated
     * id and createdAt set by the constructor.
     *
     * Used exclusively by CommentMapper when converting from CommentEntity.
     *
     * @param string                 $id         UUID string of the persisted record.
     * @param string                 $collection Name of the collection.
     * @param string                 $item       Identifier of the item.
     * @param string                 $comment    Text body of the comment.
     * @param string                 $userId     UUID of the authoring user.
     * @param DateTimeImmutable      $createdAt  Original creation timestamp from persistence.
     * @param DateTimeImmutable|null $updatedAt  Last-edit timestamp, or null if never edited.
     *
     * @return self A fully-populated Comment with the given id and timestamps.
     */
    public static function reconstitute(
        string            $id,
        string            $collection,
        string            $item,
        string            $comment,
        string            $userId,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ): self {
        $instance            = new self($collection, $item, $comment, $userId);
        $instance->id        = $id;
        $instance->createdAt = $createdAt;
        $instance->updatedAt = $updatedAt;

        return $instance;
    }

    /**
     * Returns the unique UUID of this comment record.
     *
     * @return string UUIDv7 string.
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the name of the collection this comment belongs to.
     *
     * @return string Collection name.
     */
    public function getCollection(): string
    {
        return $this->collection;
    }

    /**
     * Returns the identifier of the item this comment is attached to.
     *
     * @return string Item identifier.
     */
    public function getItem(): string
    {
        return $this->item;
    }

    /**
     * Returns the UUID of the user who authored this comment.
     *
     * @return string User UUID.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Returns the text body of the comment.
     *
     * @return string Comment text.
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Replaces the comment text and records the edit timestamp.
     *
     * @param  string $comment New text body for the comment.
     * @return static
     */
    public function setComment(string $comment): static
    {
        $this->comment   = $comment;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * Returns the UTC timestamp of when this comment was first created.
     *
     * @return DateTimeImmutable Immutable datetime in UTC.
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Returns the UTC timestamp of the last edit, or null if the comment has never been edited.
     *
     * @return DateTimeImmutable|null Last-edit timestamp, or null.
     */
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Returns true when the given user UUID matches the author of this comment.
     *
     * @param  string $userId UUID of the user to check.
     * @return bool   True if the user owns this comment.
     */
    public function isOwnedBy(string $userId): bool
    {
        return $this->userId === $userId;
    }

    /**
     * Serialises the entity to an associative array for JSON encoding.
     *
     * @return array<string, string|null> Key-value map of all entity fields.
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'collection' => $this->collection,
            'item'       => $this->item,
            'comment'    => $this->comment,
            'user'       => $this->userId,
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
            'updated_at' => $this->updatedAt?->format(DateTimeInterface::ATOM),
        ];
    }
}
