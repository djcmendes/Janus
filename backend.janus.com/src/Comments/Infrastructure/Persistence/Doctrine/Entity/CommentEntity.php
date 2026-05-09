<?php

/**
 * @file CommentEntity.php
 *
 * Doctrine ORM persistence model for the `comments` table.
 * This class is the sole owner of all database-mapping concerns for comment records.
 * Domain logic lives exclusively in Comment (Domain\Entity).
 *
 * @package App\Comments\Infrastructure\Persistence\Doctrine\Entity
 * @author  David Mendes
 */

declare(strict_types=1);

namespace App\Comments\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Doctrine entity mapping comment records to the `comments` table.
 *
 * Non-final to allow Doctrine proxy subclass generation. All persistence
 * concerns are confined here; the domain Comment class has no framework ties.
 */
#[ORM\Entity]
#[ORM\Table(name: 'comments')]
class CommentEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 200)]
    private string $collection;

    #[ORM\Column(length: 255)]
    private string $item;

    #[ORM\Column(type: 'text')]
    private string $comment;

    /** UUID of the user who wrote this comment — plain string, no ORM FK */
    #[ORM\Column(length: 36)]
    private string $userId;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Returns the UUID primary key of this record.
     *
     * @return Uuid Doctrine-managed UUID value object.
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @param Uuid $id The UUID to assign as the primary key.
     *
     * @return static
     */
    public function setId(Uuid $id): static
    {
        $this->id = $id;
        return $this;
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
     * Sets the collection name for this comment.
     *
     * @param  string $collection Collection name to store.
     * @return static
     */
    public function setCollection(string $collection): static
    {
        $this->collection = $collection;
        return $this;
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
     * Sets the item identifier for this comment.
     *
     * @param  string $item Item identifier to store.
     * @return static
     */
    public function setItem(string $item): static
    {
        $this->item = $item;
        return $this;
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
     * Sets the text body of the comment.
     *
     * @param  string $comment Comment text to store.
     * @return static
     */
    public function setComment(string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Returns the UUID of the user who authored this comment.
     *
     * @return string User UUID string.
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * Sets the UUID of the authoring user.
     *
     * @param  string $userId User UUID to store.
     * @return static
     */
    public function setUserId(string $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Returns the UTC timestamp of when this comment was first created.
     *
     * @return \DateTimeImmutable Immutable datetime in UTC.
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Sets the creation timestamp for this comment.
     *
     * @param  \DateTimeImmutable $createdAt Creation timestamp to store.
     * @return static
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Returns the UTC timestamp of the last edit, or null if never edited.
     *
     * @return \DateTimeImmutable|null Last-edit timestamp, or null.
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Sets the last-edit timestamp for this comment.
     *
     * @param  \DateTimeImmutable|null $updatedAt Last-edit timestamp, or null to clear.
     * @return static
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
